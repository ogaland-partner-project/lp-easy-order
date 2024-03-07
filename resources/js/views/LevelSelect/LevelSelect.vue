<template>
    <div class="Levelselect_main">
        <div class="manager_box">
            <div class="man_form">
                <v-text-field
                    label="販促担当者"
                    v-model="levelModel.promoter"
                    outlined
                    clearable
                    hide-details="auto"
                ></v-text-field>
            </div>
            <div class="man_form">
                <v-text-field
                    label="構成担当者"
                    v-model="levelModel.configurator"
                    outlined
                    clearable
                    hide-details="auto"
                ></v-text-field>
            </div>
            <div class="man_form">
                <v-text-field
                    label="デザイン担当者"
                    v-model="levelModel.designer"
                    outlined
                    clearable
                    hide-details="auto"
                ></v-text-field>
            </div>
        </div>

        <div class="level_select_aria">
            <div class="level_select_box_info">※お任せの構成レベルを選択してください</div>

            <v-radio-group v-model="levelModel.level">
                <div class="level_select_box">
                    <div class="level_single">
                        <div class="lebelname">
                            <v-radio label="レベルⅠ" :value="1" color="#FFFFFF"></v-radio>
                        </div>
                        <div class="levelcoment">とにかくお任せします！</div>
                        <div class="levelinfo">
                            商品詳細だけ伝え
                            <br />構成は制作で作成
                        </div>
                    </div>
                    <div class="level_single">
                        <div class="lebelname">
                            <v-radio label="レベルⅡ " :value="2" color="#FFFFFF"></v-radio>
                        </div>
                        <div class="levelcoment">半分お任せしていいですか？</div>
                        <div class="levelinfo">
                            ポイントだけ伝え
                            <br />構成は制作で作成
                        </div>
                    </div>
                    <div class="level_single">
                        <div class="lebelname">
                            <v-radio label="レベルⅢ" :value="3" color="#FFFFFF"></v-radio>
                        </div>
                        <div class="levelcoment">こだわりのLP作りたい！</div>
                        <div class="levelinfo">
                            流れは依頼者が決め
                            <br />詳細は制作で作成
                        </div>
                    </div>
                    <div class="level_single">
                        <div class="lebelname">
                            <v-radio label="レベルⅣ" :value="4" color="#FFFFFF"></v-radio>
                        </div>
                        <div class="levelcoment">LPのみの依頼</div>
                        <div class="levelinfo">
                            構成は依頼者が作成し
                            <br />薬機修正後に制作依頼
                        </div>
                    </div>
                </div>
            </v-radio-group>
        </div>

        <!-- レベル別コンテンツ読み込み -->
        <component :is="levelComponent" :levelModel="levelModel" />

        <saving-dialog v-model="saveDialog" />
    </div>
</template>

<script>
import levelOne from "./Level1.vue";
import levelTwo from "./Level2.vue";
import levelThree from "./Level3.vue";
import levelFour from "./Level4.vue";
import { initLevelModel } from "./data.js";
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
        levelOne,
        levelTwo,
        levelThree,
        levelFour
    },

    data() {
        return {
            id: "", // lp_order_id（※url params）
            levelModel: {}, // モデルオブジェクト
            initLevelModel, // 外部ファイルからオブジェクト定義読み込み（新規時用）
            newFlag: false, // 登録か更新か判定用（メソッドの振り分けで使用）
            saveDialog: false,
        };
    },

    props:['lockId'],

    created() {
        this.id = this.$route.params.id;
        this.init();
    },

    computed: {
        // 選択されているレベルに応じたコンポーネント名を返す
        levelComponent: function() {
            switch (this.levelModel.level) {
                case 1:
                    return "levelOne";
                case 2:
                    return "levelTwo";
                case 3:
                    return "levelThree";
                default:
                    return "levelFour";
            }
        }
    },

    methods: {
        ...mapActions("common", ["setSelectionMenu"]),
        // APIs----------------------------
        init(){
            this.search();
        },
        // 検索
        search: async function() {
            const result = await this.$axios({
                method: "GET",
                url: "/level_select/" + this.id
            });

            const dataObj = result.data.dataArray;
            // 新規の場合は空オブジェクトセットし、登録用フラグを立てる
            this.levelModel = dataObj ? dataObj : {};
            this.newFlag = !Object.keys(this.levelModel).length ? true : false;

            // 新規の場合はデフォルトオブジェクトセット(値渡し)
            if (this.newFlag) this.levelModel = Object.assign({},initLevelModel);

            // level2のブロックが未登録の場合初期値を設定
            if(!dataObj.t_level_select_lp_blocks.length){
                dataObj.t_level_select_lp_blocks = [{id:null,block_detail:''}];
            }

        },

        // 保存
        save: async function() {
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
                alert("保存処理に失敗しました。");
                return false;
            } finally {
                this.saveDialog = false;
                this.init();
            }
        },

        // 登録
        create: async function() {
            let params = this.levelModel;
            params.lp_order_id = this.id;
            const result = await this.$axios({
                method: "POST",
                url: "/level_select/",
                data: params
            });
        },

        // 更新
        update: async function() {
            const params = this.levelModel;
            const result = await this.$axios({
                method: "PUT",
                url: "/level_select/" + this.id,
                data: params
            });
        }
    },
};
</script>