<template>
    <div class="plan_main">
        <div class="plan_header mb-3">
            <div v-if="!getCheckEdit" class="mb-1" style="width:100%">
                <v-btn class="plan_btn" @click="folderInput" color="grey darken-1">ファイル内画像一括挿入</v-btn>
                <input v-if="fileSelected" type="file" ref="folder_input" webkitdirectory directory @change="onUpload" style="display:none;" />
            </div>
            <div class="mt-5" style="width:100%; display:flex;">
                <div style="width:55%; display:flex;">
                    <div style="width:18%">
                        <v-btn style="border-radius: 20px;" text v-if="planTab==1" @click="planTab=0">ブロックの順番</v-btn>
                        <v-btn v-if="planTab==0" class="plan_tab_btn">ブロックの順番</v-btn>
                    </div>
                    <div style="width:18%">
                        <v-btn style="border-radius: 20px;" text v-if="planTab==0" @click="planTab=1">構成案</v-btn>
                        <v-btn v-if="planTab==1" class="plan_tab_btn">構成案</v-btn>
                    </div>
                </div>
                <div style="width:45%; display:flex;">
                    <div style="width:18%">
                        <v-btn style="border-radius: 20px;" text v-if="fix==1" @click="fix=0">依頼者修正</v-btn>
                        <v-btn v-if="fix==0" class="plan_tab_btn">依頼者修正</v-btn>
                    </div>
                    <div style="width:18%">
                        <v-btn style="border-radius: 20px;" text v-if="fix==0" @click="fix=1">薬事修正内容</v-btn>
                        <v-btn v-if="fix==1" class="plan_tab_btn">薬事修正内容</v-btn>
                    </div>
                </div>
            </div>
            <div class="plan_line"></div>
            <div class="titlearia mb-2">
                <div style="width:55%; display:flex; padding-left:50px">
                    <div v-if="planTab==0" class="itembox">構成の手順から自動入力</div>
                    <div v-if="planTab==1" class="itembox" style="width:40%;">イメージ</div>
                    <div v-if="planTab==1" class="itembox" style="width:60%">文言</div>
                </div>
                <div style="width:45%; display:flex;">
                    <div class="itembox" style="width:49%">修正内容</div>
                    <div v-if="fix==1" class="itembox" style="width:50%">情報管理メモ</div>
                </div>
            </div>
        </div>
        <div style="display:flex; width:100%; position:relative">
            <div v-if="planTab==0" style="width:54.3% !important; display:flex;">
                <div style="width:3%"></div>
                <draggable
                    :disabled="checkDisabled()"
                    style="width:97%;"
                    tag="div"
                    @end="onEnd"
                    ref="drag"
                    v-model="constitutionPlans"
                    handle=".handle"
                    :options="{animation:300}"
                    :force-fallback="true"
                    :scroll-sensitivity="200"
                >
                    <div class="tejun_input_box" style="display:flex; align-items:center; flex-wrap:wrap;" v-for="(plan,n) in constitutionPlans" :key="n">
                        <div class="mb-1" style="display:flex; align-items:center; width: 95%;">
                            <span class="mr-3" style="font-size:15px; width:2%;">{{n+1}}.</span>
                            <div style="display:flex; width: 100%;">
                                <v-textarea label="キーワードを入力"
                                v-model="plan.block_detail"
                                dense
                                solo
                                flat
                                auto-grow
                                rows="1"
                                color="info"
                                background-color="rgba(153,153,153,0.1)"
                                hide-details="auto"
                                clearable
                                style="border-radius:4px 0 0 4px;"
                                :disabled="isCheckMedicineStatus()"
                                ></v-textarea>
                                <div class="drag-block-handle">
                                    <v-icon  class="handle">fas fa-bars</v-icon>
                                </div>
                            </div>
                        </div>
                        <div
                            class="tejun_input_box_delbtn mr-3"
                            style="right:18px; !important;"
                            :style="(checkDisabled() ? 'display:none;':'')"
                        >
                            <v-btn
                                @click="blockDelete(n)"
                                fab
                                x-small
                                depressed
                                color="rgb(110,110,110)"
                            ><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                        </div>
                        <v-hover v-slot="{ hover }" style="width:100%; height:10px;">
                            <div class="mb-2" style="width:100%; height:10px;">
                                <div  v-if="hover && checkDisabledHover()" @click="blockAdd(n)" class="blockadd_gyou">&nbsp;</div>
                            </div>
                        </v-hover>
                    </div>
                </draggable>
            </div>
            <draggable
                :disabled="!editorInput || checkDisabled()"
                :style="'width:'+(planTab == 0 ? '45%;':'100%;')"
                tag="div" @end="onEnd" ref="drag"
                v-model="constitutionPlans"
                :options="{animation:300}"
                :force-fallback="true"
                :scroll-sensitivity="200"
            >
                <v-hover v-slot="{ hover }" class="plan_hover_bar" v-for="(plan,n) in constitutionPlans" :key="n">
                    <div :class="hover ? 'plan_active':'none'">
                        <!-- 画像系 -->
                        <div style="width:55% !important; display:flex;" v-if="planTab==1">
                            <span style="font-size:17px; width:5%;">{{n+1}}.</span>
                            <div style="display:flex; width:95%;">
                                <div style="width:40%;">
                                    <div v-for="(img ,index) in imagePaths[n]" :key="index" style="width: 95%;">
                                        <v-hover v-if="img.image_path" v-slot="{ hover }">
                                            <div style="position:relative;" >
                                                <img :src="img.image_path" style="width:100%;" @click="imgDialogDisp(imagePaths[n],index)">
                                                <v-btn
                                                    v-if="hover && checkDisabledHover()"
                                                    class="comparison_image_delete"
                                                    @click="imgDelete(imagePaths[n],index)"
                                                    fab x-small depressed color="rgb(110,110,110)"
                                                >
                                                    <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                                                </v-btn>
                                            </div>
                                        </v-hover>
                                        <v-hover v-else v-slot="{ hover }">
                                            <div
                                                class="plan_paste_area"
                                                :contenteditable="!getCheckEdit || !isCheckMedicineStatus()"
                                                :disabled="checkDisabled()"
                                                @paste="function(e){handlePaste(e,img)}"
                                                @dragover="true"
                                                @drop.prevent="function(e){handleInput(e,img)}"
                                                @keydown="keydown"
                                            >
                                                    <div class="plan_paste_info">
                                                        <v-icon color="#999999" style="caret-color: transparent;" class="plan_paste_icon">fa-regular fa-image</v-icon>
                                                        <div class="plan_img_paste_inf" style="caret-color: transparent; pointer-events: none;">スクショをペースト</div>
                                                    </div>
                                                    <v-btn
                                                        v-if="hover && checkDisabledHover()"
                                                        @click="imgDelete(imagePaths[n],index)"
                                                        class="comparison_image_delete"
                                                        fab x-small depressed
                                                        color="rgb(110,110,110)"
                                                    >
                                                        <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                                                    </v-btn>
                                            </div>
                                        </v-hover>
                                        <v-hover v-slot="{ hover }">
                                            <div style="width:100%; height:10px;">
                                                <div v-if="hover && checkDisabledHover()" class="blockadd_gyou" @click="addImageArea(imagePaths[n],index)">&nbsp;</div>
                                            </div>
                                        </v-hover>
                                    </div>
                                    <v-hover v-if="imagePaths[n].length == 0" v-slot="{ hover }">
                                        <div style="width:100%; height:10px;">
                                            <div v-if="hover && checkDisabledHover()" class="blockadd_gyou" @click="addImageArea(imagePaths[n],index)">&nbsp;</div>
                                        </div>
                                    </v-hover>
                                </div>
                                <div style="width:60%;">
                                    <draggable
                                        v-model="memos[n]"
                                        :options="{animation:300}"
                                        :force-fallback="true"
                                        :scroll-sensitivity="200"
                                        :disabled="!editorInput || checkDisabled()"
                                    >
                                        <div style="margin-bottom:4px;" v-for="(memo,index) in memos[n]" :key="index">
                                            <v-hover v-slot="{ hover }">
                                                <div style="position:relative; display:flex;">
                                                    <div style="width:25%">
                                                        <v-combobox
                                                            v-model="memo.memo_category"
                                                            autocomplete="off"
                                                            :items="categories"
                                                            outlined
                                                            dense
                                                            :background-color="comboColor(memo.memo_category)"
                                                            :return-object="false"
                                                            item-value="text"
                                                            hide-details="auto"
                                                            class="plan_combo"
                                                            :class="'plan_combo_attach'+n+'_'+index"
                                                            density="compact"
                                                            :attach="'.plan_combo_attach'+n+'_'+index"
                                                            :disabled="isCheckMedicineStatus()"
                                                        >
                                                            <template slot="item" slot-scope="{ item }">
                                                                <v-hover v-slot="{ hover }">
                                                                    <div class="plan_pulldown" :style="'background-color:'+(hover ? item.color:'white')+';'">
                                                                        <span :style="'font-size: 14px; color:' + (hover ? 'white' : item.color) + ';'">{{item.text}}</span>
                                                                    </div>
                                                                </v-hover>
                                                            </template>
                                                        </v-combobox>
                                                    </div>
                                                    <div style="width:75%;">
                                                        <quill-editor
                                                            class="plan_editor plan_editor_block"
                                                            :class="memo.memo_category == '質問' ? 'plan_editor_question':''"
                                                            style="width:95%;"
                                                            ref="myTextEditor"
                                                            v-model="memo.memo"
                                                            @focus="editorInput=false"
                                                            @blur="editorInput=true"
                                                            :disabled="getCheckEdit"
                                                        />
                                                    </div>
                                                    <v-btn
                                                        v-if="hover && checkDisabledHover()"
                                                        class="plan_combo_delete"
                                                        @click="comboTextDelete(memos[n],index)"
                                                        fab x-small depressed
                                                        color="rgb(110,110,110)"
                                                    ><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                                                </div>
                                            </v-hover>
                                            <v-hover v-slot="{ hover }" style="width:97%; height:5px;">
                                                <div style="width:100%; height:5px;">
                                                    <div v-if="hover && checkDisabledHover()" @click="comboTextAdd(memos[n],index)" class="blockadd_gyou">&nbsp;</div>
                                                </div>
                                            </v-hover>
                                        </div>
                                    </draggable>
                                    <v-hover v-if="memos[n].length==0" v-slot="{ hover }" style="width:100%; height:10px;">
                                        <div style="width:100%; height:10px;">
                                            <div v-if="hover && checkDisabledHover()" @click="comboTextAdd(memos[n],0)" class="blockadd_gyou">&nbsp;</div>
                                        </div>
                                    </v-hover>
                                </div>
                            </div>
                        </div>
                        <!-- メモ系 -->
                        <div :style="'width:'+(planTab == 0 ? '100%;':'45%;')">
                            <div v-if="fix==0" style="width:100%; height:100%; min-height:270px;">
                                <quill-editor
                                    class="plan_editor"
                                    v-model="plan.requester_fix"
                                    @focus="editorInput=false"
                                    @blur="editorInput=true"
                                    :disabled="getCheckEdit"
                                />
                            </div>
                            <div v-if="fix==1" style="width:100%; height:100%;">
                                <div style="display:flex; height:100%; min-height:270px;">
                                    <div style="width: 50%;">
                                        <quill-editor
                                            @focus="editorInput=false"
                                            @blur="editorInput=true"
                                            class="plan_editor"
                                            style="width:95% !important;"
                                            v-model="plan.pharmaceutical_affairs_fix"
                                            :disabled="getCheckEdit"
                                        />
                                    </div>
                                    <div style="display:flex; flex-flow: column; width: 50%;">
                                        <quill-editor
                                            class="plan_editor"
                                            style="min-height: 190px; width:100% !important;"
                                            v-model="plan.information_management_memo"
                                            @focus="editorInput=false"
                                            @blur="editorInput=true"
                                            :disabled="getCheckEdit"
                                        />
                                        <v-hover v-if="plan.image_path" v-slot="{ hover }">
                                            <div style="position:relative;" >
                                                <img :src="plan.image_path" style="width:100%;" @click="imgDialogDisp([plan],0)">
                                                <v-btn
                                                    v-if="hover && !getCheckEdit"
                                                    class="comparison_image_delete"
                                                    fab x-small depressed
                                                    color="rgb(110,110,110)"
                                                    @click="plan.image_path='';plan.file='';"
                                                >
                                                    <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                                                </v-btn>
                                            </div>
                                        </v-hover>
                                        <div
                                            v-else
                                            class="plan_memo_paste_area"
                                            :contenteditable="!getCheckEdit"
                                            @paste="function(e){handlePaste(e,plan,'memo')}"
                                            @dragover="true"
                                            @drop.prevent="function(e){handleInput(e,plan,'memo')}"
                                            @keydown="keydown"
                                        >
                                            <div class="plan_memo_img_paste_inf">
                                                <v-icon style="font-size: 20px;">fa-regular fa-image</v-icon>
                                                スクショをペースト
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <v-hover v-slot="{ hover }" style="width:100%; height:10px; position:absolute; bottom: -6px;">
                            <div style="width:100%; height:10px;">
                                <div v-if="hover && checkDisabledHover()" @click="blockAdd(n)" class="blockadd_gyou">&nbsp;</div>
                            </div>
                        </v-hover>
                        <v-btn
                            v-if="hover && checkDisabledHover()"
                            class="comparison_image_delete"
                            @click="blockDelete(n)"
                            fab x-small depressed
                            color="rgb(110,110,110)"
                        >
                            <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                        </v-btn>
                    </div>
                </v-hover>
            </draggable>
        </div>
        <saving-dialog v-model="saveDialog" />
        <!-- 画像拡大表示ダイアログ -->
        <img-dialog ref="img_dialog"></img-dialog>
        <!-- 全クリア確認ダイアログ -->
        <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import ImageUtil from "@/plugins/imageUtils.js";
import { mapActions, mapGetters } from "vuex";
export default {
    components: {
        'draggable': draggable,
    },
    data(){
        return {
            lpOrderId:1,
            constitutionPlans:[],
            imagePaths:[],
            memos:[],
            planTab:1,
            fix:1,
            target:null,
            flag:true,
            categories:[
                {text:"大",color:"#E24137"},
                {text:"中",color:"#E76660"},
                {text:"小",color:"#EC8D87"},
                {text:"テキスト",color:"#999999"},
                {text:"アイコン",color:"#5A9650"},
                {text:"図/表",color:"#5A9650"},
                {text:"質問",color:"#FF784B"},
            ],
            images:[],
            editorInput:true,
            fileSelected:true,
            search_flg:false,
            saveDialog: false,
        }
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit","getSelectedProductStatus"]),
    },
    mounted(){
        this.lpOrderId = Number(this.$route.params.id);
        this.search();
    },
    methods:{
        ...mapActions("common", ["setSelectionMenu"]),
        // 画像セルに画像ペースト処理
        handlePaste:async function(e,imageObject,area){
            e.preventDefault();
            if(this.getCheckEdit) return;
            if(area != 'memo' && this.isCheckMedicineStatus()) return;
            var items = e.clipboardData.items
            if(!(items[0].type.match('image.*'))) return;
            let image = items[0].getAsFile();
            if(items[1]) image = items[1].getAsFile()
            let binary = await ImageUtil.getDataUrlFromFile(image)
            let imagePath = URL.createObjectURL(image);
            this.$set(imageObject, 'file', binary)
            this.$set(imageObject, 'image_path', imagePath)
        },
        // エクスプローラーから画像をアップロード
        handleInput:async function(e,imageObject,area){
            if(this.getCheckEdit) return;
            if(area != 'memo' && this.isCheckMedicineStatus()) return;
            let items = e.dataTransfer.files[0];
            if(items.type.match('image.*')){
                let image = items.getAsFile();
                let binary = await ImageUtil.getDataUrlFromFile(image)
                let imagePath = URL.createObjectURL(image);
                this.$set(imageObject, 'file', binary)
                this.$set(imageObject, 'image_path', imagePath)
            }
        },
        addImageArea(images,index){
            let image = {
                id:null,
                file:null,
                image_path:null,
            };
            images.splice(index+1,0,image);
        },
        // 検索
        search:async function(){
            const res = await this.$axios({
                method: "GET",
                url: "constitution_plan/"+this.lpOrderId,
            });
            this.constitutionPlans = res.data.dataArray.constitution_plan;
            this.imagePaths = res.data.dataArray.images;
            this.memos = res.data.dataArray.memos;
            this.$nextTick(()=>{
                this.search_flg = true;
            })
        },
        // 新規作成
        create:async function(){
            await this.$axios({
                method: "POST",
                url: "constitution_plan",
                data:{
                    lp_order_id:this.lpOrderId,
                    constitution_plan:this.constitutionPlans,
                    image_paths:this.imagePaths,
                    memos:this.memos
                }
            })
        },
        // 更新
        save:async function(){
            // 検索前に画面遷移されると空データを登録してしまう為。
            if(!this.search_flg) return false;
            try{
                this.saveDialog = true;
                await this.$axios({
                    method: "PUT",
                    url: "constitution_plan/"+this.lpOrderId,
                    data:{
                        lp_order_id:this.lpOrderId,
                        constitution_plan:this.constitutionPlans,
                        image_paths:this.imagePaths,
                        memos:this.memos
                    }
                }).then(()=>{
                    this.saveDialog = false;
                    this.search();
                })
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
        // 全クリア
        fn_clear(){
            this.constitutionPlans = [];
            this.imagePaths = [];
            this.memos = [];
            this.save();
        },
        blockAdd(n){
            let plan = {
                id:null,
                block_detail:null,
                requester_fix:null,
                pharmaceutical_affairs_fix:null,
                information_management_memo:null,
            };
            let imagePath = [
                { id:null, file:'', image_path:'' }
            ];
            let memo = [
                { id:null, memo_category:'大', memo:'' },
                { id:null, memo_category:'テキスト', memo:'' },
                { id:null, memo_category:'アイコン', memo:'' },
            ];
            this.constitutionPlans.splice(n+1,0,plan);
            this.imagePaths.splice(n+1,0,imagePath);
            this.memos.splice(n+1,0,memo);
        },
        blockDelete(index){
            if(this.constitutionPlans.length==1)return;
            this.constitutionPlans.splice(index,1);
            this.memos.splice(index,1);
            this.imagePaths.splice(index,1);
        },
        comboTextAdd(memos,index){
            let memo = {
                id:null,
                memo_category:'大',
                memo:''
            };
            memos.splice(index+1,0,memo);
        },
        imgDelete(images,index){
            if(images[index].image_path){
                this.$set(images, index, { id:null, file:'', image_path:'' })
                return;
            }
            images.splice(index,1);
        },
        comboTextDelete(memos,index){
            memos.splice(index,1);
        },
        // セレクトボックスに表示するカテゴリーのカラーを返す
        categoryColor(item){
            let categoryObject = this.categories.find(category=>category.text == item);
            if(categoryObject){
                return categoryObject.color;
            }
            return '#5A9650';
        },
        // LPブロックを並び替えた際に、画像、メモも並び替える
        onEnd(e){
            // 画像の配列も入れ替える
            let sort = this.imagePaths.splice(e.oldIndex,1);
            this.imagePaths.splice(e.newIndex,0,sort[0]);

            sort = this.memos.splice(e.oldIndex,1);
            this.memos.splice(e.newIndex,0,sort[0]);
        },
        folderInput(){
            this.$refs.folder_input.click();
        },
        onUpload:async function(event){
            let imageFiles = [];
            // フォルダ内の画像ファイルのみ抜き出す
            for(let index=0; index<event.target.files.length; index++){
                let file = event.target.files[index];
                if(file.type.match('image.*')){
                    imageFiles.push(file);
                }
            }
            // フォルダ内の画像枚数分繰り返し
            for(let index=0; index<imageFiles.length; index++){
                const file = imageFiles[index];
                let binary = await ImageUtil.getDataUrlFromFile(file);
                let image = {
                    id:null,
                    file:binary,
                    image_path:URL.createObjectURL(file)
                }
                if(!this.imagePaths[index]){
                    this.blockAdd(index-1)
                }
                this.$set(this.imagePaths[index],0,image);
            }
            // inputエレメントのリセット
            this.fileReset()
        },
        fileReset(){
            this.fileSelected = false
            this.$nextTick(function () {
                this.fileSelected = true
            })
        },
        // カテゴリ選択の色変更
        comboColor(category){
            let target = this.categories.find((categoryDetail)=>{
                return categoryDetail.text == category;
            })
            if(!target) return '#5A9650';
            return target.color
        },
        imgDialogDisp(image_paths,index){
            let imgDialogData = image_paths.filter((image)=>{
                return (image.image_path)
            })
            this.$refs.img_dialog.open(imgDialogData,index);
        },
        keydown(e){
            if(e.ctrlKey && e.key == 'v') return;
            e.preventDefault()
        },

        /**
         *  薬事チェック中か判断
         * - true: 薬事チェック中
         * - false: 薬事チェック中でない
         */
        isCheckMedicineStatus() {
            return this.getSelectedProductStatus == 1 || this.getSelectedProductStatus == 3 ? true : false;
        },

        /**
         * 構成のステータスが薬事確認中の場合は、情報管理しか操作しないので
         * 薬事修正内容タブの内容しか編集できないようにする(入力不可にする)
         */
        checkDisabled(){
            return this.getCheckEdit || this.isCheckMedicineStatus();
        },

        /**
         * 構成のステータスが薬事確認中の場合は、情報管理しか操作しないので
         * 薬事修正内容タブの内容しか編集できないようにする(ホバーでのボタン表示をなくす)
         */
        checkDisabledHover(){
            return !this.getCheckEdit && !this.isCheckMedicineStatus();
        },
    },
}
</script>