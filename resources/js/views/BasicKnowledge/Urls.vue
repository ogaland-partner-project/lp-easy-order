<template>
    <div class="knowledge_url_aria">
        <div class="url_aria_info">
            参考ページのURLはできる限り残しましょう！
            <div class="knowledge_add_btn text-left">
                <v-btn v-if="!getCheckEdit" @click="onEditURL">{{isActive ? "編集完了" : "URL編集" }}<v-icon color="rgb(154,217,224)">fa-solid fa-link</v-icon></v-btn>
                <div v-if="isActive" style="font-size: 0.8em;">※文字を選択後、🔗マークをクリックしてURL編集</div>
            </div>
        </div>
            
        <template v-for="model in knowledgeModels" >
            <div v-for="(url, index) in model.urls" :key="index" class="d-flex url_wrap px-2 py-2">
                <div class="url_label">
                    URL
                </div>
                <div class="url_body">
                    <quill-editor
                        v-if="isActive"
                        v-model="url.url"
                        class="pa-0 ma-0 url_text"
                        :options="option"
                    />
                    <div v-else-if="url.url" v-html="url.url" class="knowledge_url"></div>
                    <span v-else class="link-deco">未設定</span>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import 'quill/dist/quill.snow.css'
import { mapGetters } from "vuex";
export default {
    props: {
        knowledgeModels: {
            type: Array,
            default: () => []
        }
    },

    computed: {
        ...mapGetters("common", ["getCheckEdit"])
    },

    data() {
        return {
            isActive: false,            // テキストとリンクの切替用
            test_url:"",
            option:{
                theme: 'snow',
                modules:{
                    toolbar:[
                        ['link']
                    ]
                }
            }
        }
    },

    methods: {
        onEditURL(){
            this.isActive = !this.isActive;
        }
    }
}
</script>
<style scoped>
.link-deco {
    border-bottom: solid 1px #337ab7;
    font-weight: bold;
    color: #337ab7;
    cursor: pointer;
    text-overflow: ellipsis;
    overflow: hidden;
}

.url_wrap {
    border-bottom: solid 1px #cfcccc;
    align-items: center;
}

.url_wrap > .url_label {
    font-size: 1.2em;
    width: 10%;
}

.url_wrap > .url_body {
    width: 90%;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.url_body > div > p{
    margin-bottom: 0px !important;
}

</style>