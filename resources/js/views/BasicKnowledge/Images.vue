<template>
    <div class="know_fileup_aria" style="display: block;">
        <div class="fileup_box">
            <input :disabled="getCheckEdit" type="file" ref="imageInput" @change="handleInput" />
            <span class="paste" @paste="handlePaste">ここにコピー＆ペースト</span>
        </div>
        <template v-for="(model, j) in knowledgeModels" >
            <div class="fileup_prev" v-for="(image,index) in model.images" :key="index">
                <img :src="image.image_path"  @click="imgDialogDisp(model.images,index)"/>
                <v-textarea
                    label="メモ"
                    v-model="image.image_memo"
                    auto-grow
                    dense
                    hide-details="auto"
                    rows="2"
                ></v-textarea>
                <v-btn class="fileup_dell_btn" @click="deleteImg(index)" fab depressed color="#f17268">
                    <v-icon color="white" style="font-size:13px;">fa-solid fa-trash-can</v-icon>
                </v-btn>
            </div>
        </template>
        <!-- 画像拡大表示ダイアログ -->
        <img-dialog ref="img_dialog"></img-dialog>
    </div>
</template>

<script>
import ImageUtil from "@/plugins/imageUtils.js";
import { mapGetters } from "vuex";
export default {
    props: {
        knowledgeModels: {
            type: Array,
            default: () => []
        }
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },
    methods: {
        handleInput: async function() {
            let image = this.$refs.imageInput.files[0];
            if (image.type.match("image.*")) {
                let binary = await ImageUtil.getDataUrlFromFile(image);
                this.knowledgeModels[0].images.push({
                    image_path: URL.createObjectURL(image),
                    memo: "",
                    file: binary
                });
            }
        },

        handlePaste:async function(e){
            e.preventDefault();
            if(this.getCheckEdit) return;
            var items = e.clipboardData.items
            if(!(items[0].type.match('image.*'))) return;
            let image = items[0].getAsFile();
            if(items[1]) image = items[1].getAsFile()
            let binary = await ImageUtil.getDataUrlFromFile(image)
            this.knowledgeModels[0].images.push({
                image_path: URL.createObjectURL(image),
                memo: "",
                file: binary
            });
        },

        deleteImg(index) {
            this.knowledgeModels[0].images.splice(index, 1);
        },

        imgDialogDisp(image_paths,index){
            let imgDialogData = image_paths.filter((image)=>{
                return (image.image_path)
            })
            this.$refs.img_dialog.open(imgDialogData,index);
        }
    }
};
</script>

<style>
</style>