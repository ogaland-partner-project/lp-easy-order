<template>
    <div class="final_design_main">
        <!-- head -->
        <div class="fixheader mb-3">
            <div class="finalde_control_btn" v-if="!getCheckEdit">
                <v-btn @click="folderInput" color="grey darken-1">ファイル内画像一括挿入</v-btn>
                <input type="file" ref="folder_input" webkitdirectory directory @change="onUpload" v-if="fileSelected" style="display:none;"/>
            </div>
            <div class="line"></div>
            <!-- 項目名 -->
            <div class="titlearia" style="display:flex;">
                <div style="width:47%; display:flex;">
                    <div class="itembox" style="margin-left: 53px;">イメージ</div>
                    <div class="itembox" style="margin-left: 28px;">制作メモ</div>
                </div>
                <div style="width:6%;"></div>
                <div style="width:47%; display:flex;">
                    <div class="itembox" style="margin-left: 24px;">薬機修正内容</div>
                    <div class="itembox">情報管理メモ</div>
                </div>
            </div>
        </div>
        <div class="conteints"  >
            <!-- 最終デザイン行要素：削除用ホバーボタン付属 -->
            <v-hover v-slot="{ hover }" style="display:flex; width:100%; flex-wrap: wrap; position:relative; padding: 10px;" v-for="(part,index) in designParts" :key="index">
                <div :class="hover ? 'part_active':'' " >
                    <!-- index:左側の番号 -->
                    <span class="mr-5" style="font-size:17px; width:2%; height:103%;">{{index + 1}}.</span>
                    <!-- イメージ -->
                    <div style="width:96%; display:flex;">
                        <div style="width:47%; display:flex;">
                            <v-hover v-if="part.image_path" v-slot="{ hover }" style="width:50%; margin:7px;">
                                <div style="position:relative;" >
                                    <img :src="part.image_path" style="width:100%; position:relative;" @click="imgDialogDisp(index)" :draggable="false">
                                    <v-btn v-if="hover && !getCheckEdit" class="comparison_image_delete" @click="imgDelete(part)" fab x-small depressed color="rgb(110,110,110)">
                                        <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                                    </v-btn>
                                </div>
                            </v-hover>
                            <div v-else style="width:50%; display:flex;">
                                <div
                                    style="width:100%; margin:7px;"
                                    class="final_paste_area"
                                    :contenteditable="!getCheckEdit"
                                    @paste="function(e){handlePaste(e, part)}"
                                    @dragover="true"
                                    @drop.prevent="function(e){handleInput(e,part.image_path)}"
                                    @keydown="keydown"
                                >
                                    <div class="plan_paste_info">
                                        <v-icon color="#999999" style="caret-color: transparent;" class="plan_paste_icon">fa-regular fa-image</v-icon>
                                        <div class="plan_img_paste_inf" style="caret-color: transparent; pointer-events: none;">スクショをペースト</div>
                                    </div>
                                </div>
                            </div>
                            <!-- 制作メモ -->
                            <div style="width:50%; display:flex;">
                                <quill-editor
                                    class="final_editor"
                                    ref="myTextEditor"
                                    v-model="part.design_memo"
                                    :disabled="getCheckEdit"
                                />
                            </div>
                        </div>
                        <div style="width:6%;"></div>
                        <div style="display:flex; width: 47%; ">
                            <div style="width:50%; display:flex;">
                                <!-- 薬機修正内容 -->
                                <quill-editor
                                    class="final_editor"
                                    ref="myTextEditor"
                                    v-model="part.law_support_memo"
                                    :disabled="getCheckEdit"
                                />
                            </div>
                            <div style="width:50%; display:flex;">
                                <!-- 情報管理メモ -->
                                <quill-editor
                                    class="final_editor"
                                    ref="myTextEditor"
                                    v-model="part.info_manage_memo"
                                    :disabled="getCheckEdit"
                                />
                            </div>
                        </div>
                        <!-- オンマウス時に削除用（×）ボタンをホバー -->
                        <v-btn v-if="hover && !getCheckEdit" class="comparison_image_delete" @click="blockDelete(index)" fab x-small depressed color="rgb(110,110,110)">
                            <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                        </v-btn>
                    </div>
                    <!-- (+)---- 行追加要素 -->
                    <v-hover v-slot="{ hover }" style="width:97.5%; height:10px; position:absolute; bottom: -2px; z-index:2;">
                        <div style="width:100%; height:10px;">
                            <div v-if="hover && !getCheckEdit" @click="blockAdd(index)" class="blockadd_gyou">&nbsp;</div>
                        </div>
                    </v-hover>
                </div>
            </v-hover>
        </div>
        <saving-dialog v-model="saveDialog" />
        <!-- 画像拡大表示ダイアログ -->
        <img-dialog ref="img_dialog"></img-dialog>
        <!-- 全クリア確認ダイアログ -->
        <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
    </div>
</template>

<script>
import ImageUtil from "@/plugins/imageUtils.js";
import { mapActions, mapGetters } from "vuex";
export default {
    data(){
        return{
            fileSelected: true,
            id: "", // lp_order_id（※url params）
            designParts: {
            }, // APIの実行結果格納用
            newRowDesignParts: {
                id:null,
                lp_order_id:this.id,
                image_path:'',
                file:'',
                design_memo:'',
                law_support_memo:'',
                info_manage_memo:''
            },
            content: "",
            search_flg:false,
            saveDialog: false,
        }
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },
    mounted() {
        this.lpOrderId = this.$route.params.id;
        this.search();
    },

    methods:{
        ...mapActions("common", ["setSelectionMenu"]),
        // 同一フォルダの選択を可能にする。
        fileSelectreset () {
        this.fileSelected = false
            this.$nextTick(function () {
                this.fileSelected = true
            })
        },
        // APIs----------------------------
        // 検索
        search: async function() {
            const result = await this.$axios({
                method: "GET",
                url: "/final_design_confirmation/" + this.lpOrderId
            });

            // 抽出データ取得
            this.designParts = result.data.dataArray.design_parts;

            // length取得
            let i = result.data.dataArray.design_parts.length;

            // 検索結果が0件の場合、新規登録用の空行を追加
            if ( i == 0 ){
                this.blockAdd(i);
            }
            this.$nextTick(()=>{
                this.search_flg = true;
            })
        },

        save: async function() {
            // 検索前に画面遷移されると空データを登録してしまう為。
            if(!this.search_flg) return false;
            try{
                this.saveDialog = true;
                // 除外対象のデータを除いた配列を作成
                const filteredData = this.designParts.filter(part => {
                    // idとlp_order_idとsort_order以外の項目が全てnullの場合にfalseを返す
                    const values = Object.values(part).filter((
                        value =>
                            value !== part.id &&
                            value !== part.lp_order_id &&
                            value !== part.sort_order));
                    // 空文字をnullに変換（入力項目内を削除した場合の対処用）
                    const nonEmptyValues = values.map(value => value === "" ? null : value);
                    return nonEmptyValues.some(value => value !== null);
                });
                // リクエスト
                await this.$axios({
                    method: "PUT",
                    url: "final_design_confirmation/" + this.lpOrderId,
                    data: {
                    lp_order_id: this.lpOrderId,
                    design_parts: filteredData
                    }
                });
                // 再検索
                this.saveDialog = false;
                this.search();
                return true;
            }catch(error){
                alert('保存に失敗しました')
                return false;
            }
        },

        // 全クリアダイアログ表示
        clear(){
            this.$refs.clear.open();
        },

        // 初期化
        fn_clear() {
            this.designParts = [];
            // 新規登録用の空行を追加
            this.blockAdd(0);
        },

        // 空行の追加
        blockAdd(index){
            const addRow = {
                id: null,
                lp_order_id:this.lpOrderId,
                image_path: null,
                file: null,
                design_memo: null,
                law_support_memo: null,
                info_manage_memo: null,
                sort_order: null
            };
            this.designParts.splice(index + 1 , 0 , addRow);
        },

        // クリップボードの画像をアップロード
        handlePaste:async function(e,part){
            e.preventDefault();
            if(this.getCheckEdit) return;
            var items = e.clipboardData.items
            if(!(items[0].type.match('image.*'))) return;
            let image = items[0].getAsFile();
            if(items[1]) image = items[1].getAsFile()
            let binary = await ImageUtil.getDataUrlFromFile(image)
            let imagePath = URL.createObjectURL(image);
            part.file = binary;
            part.image_path = imagePath;
        },

        handleInput:async function(e,images){
            let image = e.dataTransfer.files[0];;
            let binary = await ImageUtil.getDataUrlFromFile(image)
            images.push(
                {
                    id:null,
                    file:binary,
                    image_path:URL.createObjectURL(image),
                },
            )
        },

        // 行削除
        blockDelete(index){
            if(this.designParts.length==1)return;
            this.designParts.splice(index,1);
        },

        // ファイル内画像一括挿入用フォルダ選択
        folderInput(){
            this.$refs.folder_input.click();
        },

        // ファイルアップロードイベント
        async onUpload(event) {
            let imageFiles = [];
            // フォルダ内の画像ファイルのみ抜き出す
            for(let index=0; index<event.target.files.length; index++){
                let file = event.target.files[index];
                if(file.type.match('image.*')){
                    imageFiles.push(file);
                }
            }
            // フォルダ内の画像枚数分繰り返し
            for (let i = 0; i < imageFiles.length; i++) {
                // ファイル情報設定
                const file = imageFiles[i];
                if (file.type.match('image.*')) {
                    const binary = await ImageUtil.getDataUrlFromFile(file);
                    const image = {
                        id: null,
                        lp_order_id: this.lpOrderId,
                        image_path: URL.createObjectURL(file),
                        file: binary,
                        design_memo: null,
                        law_support_memo: null,
                        info_manage_memo: null,
                    };
                    // デザインパーツリストに順番に追加する
                    if(this.designParts[i]){
                        this.$set(this.designParts[i],'image_path',URL.createObjectURL(file));
                        this.$set(this.designParts[i],'file',binary);
                    }else{
                        this.designParts.push(image);
                    }
                }
            }
            // 画面上でデータが入力されていない行は削除
            this.removeRow();
            this.fileSelectreset();

        },
        // 画像だけ削除
        imgDelete(part){
            part.image_path = '';
            part.file = '';
        },
        // 不要行の削除
        // lp_order_idを除くプロパティがNullもしくは空文字列だった場合は、対象行を削除します。
        removeRow() {
            for (let i = this.designParts.length - 1; i >= 0; i--) {
                const designPart = this.designParts[i];
                let isNull = true;
                for (const prop in designPart) {
                    if (prop !== "lp_order_id" && designPart[prop]) {
                        isNull = false;
                        break;
                    }
                }
                if (isNull) {
                this.designParts.splice(i, 1);
                }
            }
        },
        imgDialogDisp(index){
            this.$refs.img_dialog.open(this.designParts,index);
        },
        keydown(e){
            if(e.ctrlKey && e.key == 'v') return;
            e.preventDefault()
        }


    }
}
</script>