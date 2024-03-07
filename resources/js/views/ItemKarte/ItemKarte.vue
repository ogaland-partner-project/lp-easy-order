<template>
    <div class="item_karte_main">
        <v-row>
            <v-col cols="6" style="margin-top: 30px;">
                <div class="karte_edit_box">
                    <!--　入力フィールドのエラーメッセージ余白削除は　hide-details="auto"　-->
                    <v-text-field
                        label="商品名"
                        outlined
                        placeholder=""
                        persistent-placeholder
                        hide-details="auto"
                        class="mb-3"
                        v-model="itemKarte.goods_name"
                    ></v-text-field>
                    <v-text-field
                        label="商品仕様（販売単位）"
                        outlined
                        placeholder=""
                        persistent-placeholder
                        hide-details="auto"
                        class="half_width mb-3"
                        v-model="itemKarte.goods_specifications"
                    ></v-text-field>
                    <v-text-field
                        label="税込販売価格"
                        type="number"
                        outlined
                        placeholder=""
                        persistent-placeholder
                        hide-details="auto"
                        class="half_width mb-3"
                        v-model="itemKarte.price_including_tax"
                    ></v-text-field>
                    <v-textarea
                        label="コンセプト"
                        outlined
                        placeholder=""
                        persistent-placeholder
                        hide-details="auto"
                        class="mb-4 outlined_field"
                        rows="1"
                        auto-grow
                        v-model="itemKarte.concept"
                    ></v-textarea>
                    <div class="karte_multiple_box">
                        <div class="w-half">
                            <v-select
                                label="性別"
                                :items="genderSelect"
                                hide-details="auto"
                                v-model="itemKarte.target_jendar"
                            ></v-select>
                        </div>
                        <div class="w-half">
                            <v-text-field
                                label="年代"
                                hide-details="auto"
                                v-model="itemKarte.target_age"
                            ></v-text-field>
                        </div>
                        <div class="w-full">
                            <v-textarea
                                label="こんな人"
                                hide-details="auto"
                                auto-grow
                                rows="1"
                                v-model="itemKarte.target_statue"
                            ></v-textarea>
                        </div>
                    </div>
                    <div class="karte_BM_box">
                        <div class="w-half">
                            <v-text-field
                                label="BM商品名"
                                hide-details="auto"
                                v-model="itemKarte.BM_goods_name1"
                            ></v-text-field>
                        </div>
                        <div class="w-half">
                            <v-text-field
                                v-if="!getCheckEdit"
                                label="BM URL"
                                hide-details="auto"
                                v-model="itemKarte.BM_url1"
                            ></v-text-field>
                            <div v-else>
                                <div>BM URL</div>
                                <a :href="itemKarte.BM_url1" target="_blank">
                                    <span class="blue--text text-decoration-underline">{{ itemKarte.BM_url1 }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="genryo_add_box" v-for="(material,index) in materials" :key="index">
                        <v-textarea
                            label="使用原料・成分など"
                            outlined
                            placeholder=""
                            persistent-placeholder
                            hide-details="auto"
                            rows="1"
                            auto-grow
                            class="mb-3 outlined_field"
                            v-model="material.raw_material_name"
                        ></v-textarea>
                        <div class="genryo_del_btn" v-if="!getCheckEdit">
                            <v-btn
                                @click="deleteMaterial(index)"
                                fab
                                x-small
                                depressed
                                color="rgb(110,110,110)"
                            >
                                <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                            </v-btn>
                        </div>
                    </div>
                    <div class="blockadd_gyou" @click="addMaterial"  v-if="!getCheckEdit">&nbsp;</div>
                </div>
            </v-col>

            <v-col cols="6" style="margin-top: 30px;">
                <div class="karte_edit_box">
                    <v-textarea
                        label="差別化ポイント"
                        outlined
                        auto-grow
                        hide-details="auto"
                        clear-icon="mdi-close-circle"
                        rows="11"
                        class="mb-3 outlined_field"
                        v-model="itemKarte.difference_point"
                    ></v-textarea>

                    <v-textarea
                        label="強み"
                        outlined
                        auto-grow
                        hide-details="auto"
                        clear-icon="mdi-close-circle"
                        rows="8"
                        class="mb-3 outlined_field"
                        v-model="itemKarte.strong_point"
                    ></v-textarea>

                    <v-textarea
                        label="メモ"
                        outlined
                        auto-grow
                        hide-details="auto"
                        rows="3"
                        clear-icon="mdi-close-circle"
                        v-model="itemKarte.memo"
                        class="outlined_field"
                    ></v-textarea>
                </div>
            </v-col>
        </v-row>
        <saving-dialog v-model="saveDialog" />
        <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
    data() {
        return {
            id: "", // lp_order_id（※url params）
            genderSelect: ["男", "女","男女どちらも"],
            itemKarte: {}, // APIの実行結果格納用
            materials: [], // APIの実行結果格納用
            newFlag: false, // 登録か更新か判定用（メソッドの振り分けで使用）
            saveDialog: false,
            search_flg:false,
        };
    },
    props:['lockId'],

    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },

    created() {
        this.id = this.$route.params.id;
        this.search();
    },

    methods: {
        ...mapActions("common", ["setSelectionMenu"]),
        // APIs----------------------------
        // 検索
        search: async function() {
            const result = await this.$axios({
                method: "GET",
                url: "/item_karte/" + this.id
            });

            const dataObj = result.data.dataArray[0];

            // 新規の場合は空オブジェクトセットし、登録用フラグを立てる
            this.itemKarte = dataObj ? dataObj : {};
            this.newFlag = !Object.keys(this.itemKarte).length ? true : false;
            // マテリアル情報
            this.materials = dataObj ? dataObj.material_list : [{id:null,item_karte_id:null,production_area:''}];
            this.$nextTick(()=>{
                this.search_flg = true;
            })
        },

        // 保存
        save: async function() {
            // 検索前に画面遷移されると空データを登録してしまう為。
            if(!this.search_flg) return false;
            try {
                this.saveDialog = true;
                // 振り分け
                if (this.newFlag) {
                    await this.create();
                    return true;
                }
                await this.update();
                return true;
            } catch (error) {
                return false;
            } finally {
                this.saveDialog = false;
            }
        },

        // 登録
        create: async function() {
            const params = this.itemKarte;
            params.lp_order_id = this.id;
            params.material_list = this.materials;
            const result = await this.$axios({
                method: "POST",
                url: "/item_karte/",
                data: params
            });
        },

        // 更新
        update: async function() {
            const params = this.itemKarte;
            const result = await this.$axios({
                method: "PUT",
                url: "/item_karte/" + params.id,
                data: params
            });
        },

        // 動き------------------------------------------------------
        // 原料部分の枠追加
        addMaterial() {
            this.materials.push({
                raw_material_name: ""
            });
        },

        // 原料部分の枠削除
        deleteMaterial(index) {
            this.materials.splice(index, 1);
        },

        // 全クリアダイアログ表示
        clear(){
            this.$refs.clear.open();
        },

        // 初期化
        fn_clear() {
            const id = this.itemKarte.id;
            this.materials = [
                {
                    raw_material_name: ""
                }
            ];
            this.itemKarte = {
                id:id,
                goods_name:'',
                goods_specifications:'',
                price_including_tax:null,
                concept:'',
                target_jendar:'',
                target_age:'',
                target_statue:'',
                BM_goods_name1:'',
                BM_url1:'',
                difference_point:'',
                strong_point:'',
                memo:'',
                material_list:[],
            };
        }
    }
};
</script>
