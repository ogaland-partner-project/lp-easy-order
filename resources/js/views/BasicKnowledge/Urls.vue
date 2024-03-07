<template>
    <div class="knowledge_url_aria">
        <div class="url_aria_info">
            参考ページのURLはできる限り残しましょう！
            <div style="font-size: 0.8em;">※URLを右クリックで編集モード</div>
        </div>
        <template v-for="model in knowledgeModels" >
            <div v-for="(url, index) in model.urls" :key="index" class="d-flex url_wrap px-2 py-2">
                <div class="url_label">
                    URL
                </div>
                <div class="url_body">
                    <v-text-field
                        v-if="isActive"
                        class="pa-0 ma-0 url_text"
                        v-model="url.url"
                        clearable
                        dense
                        hide-details="auto"
                        @input="isActive = true"
                        :ref="'url_'+index"
                        @blur="urlBlur"
                    ></v-text-field>
                    <span v-else class="link-deco" @click="onlinkClick(url.url)" @click.right.prevent="rightClick(index)">{{ url.url ? url.url : '未設定' }}</span>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        knowledgeModels: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            isActive: false,            // テキストとリンクの切替用
        }
    },

    methods: {
        onlinkClick(url) {
            if(url) {
                // 別タブでリンクを開く
                window.open(url, '_blank')
            }
            return;
        },
        // 右クリック時入力モードにする
        rightClick(index){
            this.isActive = true
            this.$nextTick(()=>{
                let ref = 'url_'+index;
                this.$refs[ref][0].focus();
            })
        },
        // url入力からカーソルを外した時入力モードを終了する
        urlBlur(e){
            // 別のurl入力欄にカーソルを移した時は入力モードを終わらせないようにする処理
            if(e.relatedTarget){
                let classes = e.relatedTarget.parentElement.parentElement.parentElement.parentElement.classList
                if(classes.contains('url_text')) return
            }
            this.isActive = false
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
}

.url_wrap > .url_label {
    font-size: 1.2em;
    width: 10%;
}

.url_wrap > .url_body {
    width: 90%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

</style>