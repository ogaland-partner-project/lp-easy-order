<template>
    <div>
        <div class="knowledge_add_btn">
            <v-btn v-if="!getCheckEdit" @click="onAddKnowledge">商品/原材料名 追加<v-icon color="rgb(154,217,224)">fa-solid fa-plus</v-icon></v-btn>
        </div>
        <template v-if="detailNum != 1">
            <div id="topbar" class="scroll-x-topbar" v-scroll.self="onScrollXTopBar">
                <div class="inner-topbar"  :style="'width:'+knowledgeWidth+'px;'"></div>
            </div>
            <div id="content" class="knowledge_add_aria" ref="knowledgeContent"  v-scroll.self="onScrollXContent">
                <knowledge :knowledgeModels="knowledgeModels" :key="resetKey+'knowledge'" />
            </div>
            <div class="knowledge_second_session">
                <images :knowledgeModels="knowledgeModels" :key="resetKey+'images'" />
                <urls :knowledgeModels="knowledgeModels" :key="resetKey+'urls'" />
            </div>
        </template>
        <template v-else>
            <div class="d-flex" style="padding: 20px;">
                <knowledge :knowledgeModels="knowledgeModels" :key="resetKey+'knowledge'" />
                <images :knowledgeModels="knowledgeModels" :key="resetKey+'images'" />
            </div>
            <urls :knowledgeModels="knowledgeModels" :key="resetKey+'urls'" />
        </template>
        <saving-dialog v-model="saveDialog" />
        <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
    </div>
</template>

<script>
import { initKnowledgeModel, initKnowledgeItems, initUrlModel } from './data.js';
import Knowledge from './Knowledge.vue';
import Urls from './Urls.vue';
import Images from './Images.vue';
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
        Knowledge,
        Urls,
        Images
    },

    data() {
        return {
            id: "", // lp_order_id（※url params）
            knowledgeModels: [], //
            initKnowledgeItems,
            initKnowledgeModel,
            initUrlModel,
            newFlag: false, // 登録か更新か判定用（メソッドの振り分けで使用）
            saveDialog: false,
            resetKey: 0, // クリアボタンが押されたらkeyを更新して子コンポーネントのデータ更新をする
            knowledgeWidth:0,
            search_flg:false,
        }
    },

    created() {
        this.id = this.$route.params.id;
        this.search();
    },

    computed: {
        ...mapGetters("common", ["getCheckEdit"]),

        detailNum() {
            // 親からまだデータが渡ってきてない場合は0を返す
            const obj = this.knowledgeModels[0];
            if(obj == void 0) {
                return 0;
            }
            return this.knowledgeModels[0].details.length;
        }
    },

    //基礎知識上部スクロールバーの幅セット
    updated(){
        if(this.$refs.knowledgeContent){
            let ref = this.$refs.knowledgeContent.firstElementChild.firstElementChild;
            if(ref.classList[0] == 'd-flex'){
                this.knowledgeWidth = ref.scrollWidth + 20;
            }
        }
    },

    methods: {
        ...mapActions("common", ["setSelectionMenu"]),
        // APIs----------------------------
        // 検索
        search: async function() {
            const result = await this.$axios({
                method: "GET",
                url: "/basic_knowledge/" + this.id
            });

            const dataObj = result.data.dataArray;
            this.knowledgeModels =dataObj;
            this.newFlag = !Object.keys(this.knowledgeModels).length ? true : false;
            // 新規の場合は初期オブジェクトをセット
            if(this.newFlag) {
                this.knowledgeModels[0] = this.initKnowledgeModel;
                if(this.knowledgeModels[0].details.length == 0){
                    this.onAddKnowledge();
                }
            }

            // URLが10枠無い場合は補填
            this.addInitUrl();
            this.initKnowledgeModel.basic_knowledge_id = this.knowledgeModels[0].basic_knowledge_id;
            this.$nextTick(()=>{
                this.search_flg = true;
            })
        },

        save:async function(){
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
                this.search();
                return true;
            }
        },

        // 登録
        create: async function() {
            const params = this.knowledgeModels[0];
            params.lp_order_id = this.id;
            const result = await this.$axios({
                method: "POST",
                url: "/basic_knowledge/",
                data: params
            });
        },

        // 更新
        update: async function() {
            const params = this.knowledgeModels[0];
            const result = await this.$axios({
                method: "PUT",
                url: "/basic_knowledge/" + params.basic_knowledge_id,
                data: params
            });
        },

        // 全クリアダイアログ表示
        clear(){
            this.$refs.clear.open();
        },

        // 既存データのクリア処理
        fn_clear() {
            // オブジェクト初期化
            this.knowledgeModels[0] = JSON.parse(JSON.stringify(this.initKnowledgeModel));
            this.onAddKnowledge();
            this.addInitUrl();
            this.resetKey = this.resetKey + 1;
            this.save();
        },

        // URLの10枠を補填（初期表示のために）
        addInitUrl() {
            let urlCount = this.knowledgeModels[0].urls.length;
            for (let index = 0; index < 10 - urlCount; index++) {
                let obj = { ...this.initUrlModel };
                this.knowledgeModels[0].urls.push(obj);
            }
        },

        // 列自体を追加
        onAddKnowledge() {
            let cols = this.knowledgeModels[0].details.length;  // 後でインデックス扱いになるため-1

            let details = this.initKnowledgeItems.map(function(item) {
                let copy = {...item};
                copy.col = cols + 1;
                return copy;
            });
            this.knowledgeModels[0].details.push(details);
        },

        // スクロールバー上下同期処理
        onScrollXTopBar(e) {
            document.getElementById("content").scrollTo(e.target.scrollLeft, 0);
        },
        onScrollXContent(e) {
            document.getElementById("topbar").scrollTo(e.target.scrollLeft, 0);
        },
    },
}
</script>