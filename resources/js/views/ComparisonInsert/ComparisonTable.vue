<template>
        <div>
            <!-- テーブル部分 -->
            <v-simple-table id="comparison_table">
                <template v-slot:default>
                    <thead>
                        <draggable
                            :disabled="getCheckEdit"
                            tag="tr"
                            :sort="true"
                            v-model="headers"
                            @end="headerMove"
                            ref="drag"
                            :options="{animation:300}"
                            :force-fallback="true"
                            :scroll-sensitivity="200"
                        >
                            <template v-for="(header,index) in headers">
                                <th v-if="parentJudge(header)" style="position:relative;" @dblclick="headerEdit(header)" @click="show=false" :key="index">
                                    <span>{{header.header_name}}</span>
                                    <b v-if="index == 0 && !getCheckEdit" @click="headerAdd(index,'top')" class="th_top"></b>
                                    <b v-if="!getCheckEdit" @click="headerAdd(index,'bottom')" class="th_bottom"></b>
                                </th>
                            </template>
                        </draggable>
                    </thead>
                    <draggable
                        :disabled="getCheckEdit"
                        tag="tbody" v-model="items"
                        @end="save()"
                        :options="{animation:300}"
                        :force-fallback="true"
                        :scroll-sensitivity="200"
                    >
                        <tr v-for="(item,Iindex) in items" :key="Iindex" style="position:relative;">
                            <template v-for="(cell,Cindex) in item">
                                <td
                                    v-if="parentJudge(cell)"
                                    :key="Cindex"
                                    @click="editMode(cell,Iindex,Cindex,headers[Cindex].header_type)"
                                >
                                    <v-textarea auto-grow v-if="cell.editable==true && headers[Cindex].header_type != 'calculation' && headers[Cindex].header_type != 'image'" :ref="'index_'+Iindex+'_'+Cindex" hide-details  v-model="cell.text" class="editArea"  @blur="cell.editable=false" rows="1"/>
                                    <div v-else-if="headers[Cindex].header_type == 'image'">
                                        <v-hover v-if="cell.text" class="hikaku_img_prev" v-slot="{ hover }">
                                            <div>
                                                <div style="position:relative;">
                                                <img :src="imgCacheClear(cell.text)" class="comparison_image"  @click="imgDialogDisp(cell.text)">
                                                <v-btn v-if="hover && !getCheckEdit" class="comparison_image_delete" @click="cell.text=''" fab x-small depressed color="rgb(110,110,110)"><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                                                </div>
                                            </div>
                                        </v-hover>
                                        <div v-else :contenteditable="!getCheckEdit" pattern="^[a-zA-Z0-9]+$" @keydown="keydown" class="hikaku_img_paste" @paste="function(e){handlePaste(e,cell)}" @dragover="true" @drop.prevent="function(e){handleInput(e,cell)}">
                                                <v-icon>fa-regular fa-image</v-icon><br>
                                                <div class="hikaku_img_paste_inf">スクショをペースト</div>
                                        </div>
                                    </div>
                                    <span v-else-if="headers[Cindex].header_type == 'text'">{{cell.text}}</span>
                                    <a v-else-if="linkDisp(headers[Cindex],'a')" :href="cell.text" target="_blank" rel="noopener noreferrer">{{cell.text}}</a>
                                    <span v-else-if="linkDisp(headers[Cindex],'span')" >{{cell.text}}</span>
                                    <span
                                        v-else-if="headers[Cindex].header_type == 'calculation'"
                                        :style="calculation(headers[Cindex],item) == '未計算' ? 'color:#A992E1;':''"
                                    >{{calculation(headers[Cindex],item)}}</span>
                                </td>
                            </template>
                            <b v-if="!getCheckEdit" class="td_left" @click="itemAdd(Iindex,'left')"></b>
                            <b v-if="Iindex+1 == items.length && !getCheckEdit" class="td_right" @click="itemAdd(Iindex,'right')"></b>
                            <v-hover v-if="!getCheckEdit" v-slot="{ hover }" style="position:absolute; top:0; right:0; width:20px; height:20px;">
                                <div style="position:relative; top:0; right:0; width:30px; height:30px; z-index:3;">
                                    <v-btn v-if="hover" @click="rowDelete(Iindex)" class="comparison_image_delete" fab x-small depressed color="rgb(110,110,110)"><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                                </div>
                            </v-hover>
                        </tr>
                    </draggable>
                </template>
            </v-simple-table>
            <!-- ヘッダー編集ダイアログ -->
            <header-dialog ref="header_dialog" :headers="headers" :targetHeader="targetHeader" :parent="parent" @headerDelete="headerDelete"></header-dialog>
            <!-- 画像拡大表示ダイアログ -->
            <img-dialog ref="img_dialog"></img-dialog>
        </div>
</template>

<script>
import ImageUtil from "@/plugins/imageUtils.js";
import { $WebMsg } from "@/_CommonJs/WebMsg.js";
import draggable from 'vuedraggable';
import HeaderDialog from './HeaderDialog.vue';
import { mapGetters } from "vuex";
export default {
    components: {
    'draggable': draggable,
    'header-dialog':HeaderDialog,
    },
    props: {
        parent:{
            type: String,
            default: () => ''
        }
    },
    data() {
        return {
            lpOrderId:1,
            headers:[],
            items: [],
            pasteImage:null,
            pasteData:"",
            targetCell:{editable:false},
            headerEditDialog:false,
            menu_position: {
                left: 0,
                top: 0,
                targetRow:null,
                targetColmun:null,
                color:"#FFFFFF",
            },
            targetHeader:{header_name:'',header_type:'text',calculation_type:'',calculation_row:[]},
            search_flg:false,
        };
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
        linkDisp(){
            // セルがURLの場合、編集モードでない時のみリンクにする処理
            return (header,dom)=>{
                if(header.header_type == 'url' && this.getCheckEdit && dom == 'a'){
                    return true;
                }
                if(header.header_type == 'url' && !this.getCheckEdit && dom == 'span'){
                    return true;
                }
                return false;
            }
        }
    },
    mounted(){
        this.lpOrderId = Number(this.$route.params.id);
        if(this.lpOrderId){
            this.search();
        }
    },
    methods: {
        // APIs------------------------------------------------------
        // 検索処理
        search:async function(){
            const res = await this.$axios({
                method: "GET",
                url: "comparison_insert/"+this.lpOrderId,
            });
            this.headers = res.data.dataArray.headers;
            // observerで参照できなかったので変換
            this.items = Object.keys(res.data.dataArray.items).map(function (key) {return res.data.dataArray.items[key];});
            // 何も登録されていなかったら初期登録処理
            if(this.headers.length == 0){
                this.create();
            }
            this.$nextTick(()=>{
                this.search_flg = true;
            })
        },
        // 初期登録
        create:async function(){
            const res = await this.$axios({
                method: "POST",
                url: "comparison_insert",
                data:{
                    lp_order_id:this.lpOrderId,
                }
            }).then(()=>{
                this.search();
            });
        },
        // 更新処理
        save:async function(){
            // 検索前に画面遷移されると空データを登録してしまう為。
            if(!this.search_flg) return false;
            try{
                const res = await this.$axios({
                    method: "PUT",
                    url: "comparison_insert/"+this.lpOrderId,
                    data:{
                        headers:this.headers,
                        items:this.items,
                    }
                }).then(()=>{
                    this.search();
                });
                return true;
            }catch(error){
                alert('保存に失敗しました')
                return false;
            }
        },
        // 動き------------------------------------------------------
        // セルの計算処理
        calculation(header,items){
            let result = 0;
            if(header.calculation_type == 'sum' || header.calculation_type == 'average'){
                header.calculation_row.forEach(headerIndex => {
                    let index = this.headers.findIndex( head => { return head.id == headerIndex });
                    result = Number(items[index].text) + Number(result);
                });
            }
            // 平均の算出
            if(header.calculation_type == 'average'){
                result = result / header.calculation_row.length;
            }
            // 割り算の算出
            if(header.calculation_type == 'division'){
                let index = this.headers.findIndex( head => { return head.id == header.calculation_row[0] });
                let index2 = this.headers.findIndex( head => { return head.id == header.calculation_row[1] });
                result = Number(items[index].text) / Number(items[index2].text);
                if(isNaN(result) || !isFinite(result)) result = '未計算'
            }
            // 引き算の算出
            if(header.calculation_type == 'subtraction'){
                let index = this.headers.findIndex( head => { return head.id == header.calculation_row[0] });
                let index2 = this.headers.findIndex( head => { return head.id == header.calculation_row[1] });
                result = Number(items[index].text) - Number(items[index2].text);
            }
            return result;
        },

        // セルのクリックで編集モード
        editMode(cell,Iindex,Cindex,header){
            if(this.getCheckEdit) return;
            cell.editable = true;
            let ref = 'index_'+Iindex+'_'+Cindex;
            if(header == "image"){
                this.targetCell = cell;
                return;
            }
            this.$nextTick(() => this.$refs[ref][0].focus());
        },

        // 画像セルに画像ペースト処理
        // 画像セルに画像ペースト処理
        handlePaste:async function(e,cell){
            e.preventDefault();
            if(this.getCheckEdit) return;
            this.targetCell = cell;
            var items = e.clipboardData.items
            if(!(items[0].type.match('image.*'))) return;
            let image = items[0].getAsFile();
            if(items[1]) image = items[1].getAsFile()
            let extension = image.name.substring(image.name.lastIndexOf('.'));
            let path = '/storage/lp_order/'+this.lpOrderId+'/ComparisonInsert/'+this.targetCell.id+extension;
            this.targetCell.path = path;
            this.targetCell.text = URL.createObjectURL(image);
            this.targetCell.file = await ImageUtil.getDataUrlFromFile(image);
            this.targetCell.extension = extension;
            this.targetCell.editable = false;
            this.targetCell = {editable:false};
        },

        // 画像セルに画像ドロップ処理
        handleInput:async function(e,cell){
            if(this.getCheckEdit) return;
            this.targetCell = cell;
            let image = e.dataTransfer.files[0];
            let extension = image.name.substring(image.name.lastIndexOf('.'));
            let path = '/storage/lp_order/'+this.lpOrderId+'/ComparisonInsert/'+this.targetCell.id+extension;
            this.targetCell.path = path;
            this.targetCell.text = URL.createObjectURL(image);
            this.targetCell.file = await ImageUtil.getDataUrlFromFile(image);
            this.targetCell.extension = extension;
            this.targetCell.editable = false;
            this.targetCell = {editable:false};
        },
        // ヘッダー編集ダイアログ表示処理
        headerEdit(header){
            this.targetHeader = header;
            this.$refs.header_dialog.open();
        },

        // ヘッダー(行)削除
        headerDelete(){
            let targetCalculation = this.headers.filter(head =>{
                return head.calculation_row.includes(this.targetHeader.id) && head.header_type == 'calculation'
            })
            if(targetCalculation.length){
                let rows = '';
                targetCalculation.forEach(caluc => {
                    rows+= '「'+caluc.header_name+'」'
                },)
                $WebMsg.InfWarning(rows+'の計算対象になっている為、削除できません');
                return;
            }
            let index = this.headers.findIndex( head => { return head.id == this.targetHeader.id });
            this.headers.splice(index,1);
            this.items.forEach(item => {
                item.splice(index,1);
            })
            this.$refs.header_dialog.close();
        },

        // ヘッダー追加処理
        headerAdd(headerIndex,position){
            let index = headerIndex;
            if(position == 'bottom') index++;
            this.headers.splice(index,0,{
                id:null,
                header_name:'',
                header_type:'text',
                calculation_type:'',
                calculation_row:[],
                comparison_insert_flag:this.parent == 'insert' ? 1 : 0,
                companies_comparison_flag:this.parent == 'companies' ? 1 : 0
                }
            );
            Object.keys(this.items).forEach(item => {
                this.items[item].splice(index,0,{text:'',color:'#FFFFFF',editable:false});
            })
            this.save()
        },

        // 列追加処理
        itemAdd(Iindex,position){
            let index = Iindex;
            if(position == 'right') index++;
            let row = [];
            this.headers.forEach(val=>{
                row.push({id:null,text:'',color:'#FFFFFF',comparison_insert_flag:val.comparison_insert_flag,companies_comparison_flag:val.companies_comparison_flag,editable:false});
            })
            this.items.splice(index,0,row);
            this.save()
        },

        // ヘッダーの並び替え処理
        headerMove(e){
            this.items.forEach((v,index)=>{
                this.items[index]=this.moveAt(v,e.oldDraggableIndex,e.newDraggableIndex)
            })
            this.save();
        },

        moveAt(array, index, at) {
            if (index === at || index > array.length -1 || at > array.length - 1) {
            return array;
            }
            const value = array[index];
            const tail = array.slice(index + 1);
            array.splice(index);
            Array.prototype.push.apply(array, tail);
            array.splice(at, 0, value);
            return array;
        },

        // 列の削除
        rowDelete(targetRow){
            this.items.splice(targetRow,1);
        },

        // 他社比較入力の項目を表示するか、他社構成比較の項目を表示するかの判断
        parentJudge(object){
            if(this.parent == 'insert'){
                return object.comparison_insert_flag
            }
            if(this.parent == 'companies'){
                return object.companies_comparison_flag
            }
        },

        // 画像の拡大表示
        imgDialogDisp(image_path){
            let imgDialogData = [{image_path:image_path}]
            this.$refs.img_dialog.open(imgDialogData,0);
        },

        /**
         * 画像のペーストエリアに文字を入力させない処理
         * ペーストエリアで「ctrl + V」以外のキー操作を全て無視する
         */
        keydown(e){
            if(e.ctrlKey && e.key == 'v') return;
            e.preventDefault()
        },

        /**
         * 画像のキャッシュを読み込ませない処理
         * 画像を更新してもキャッシュから読み込んでしまい、見た目が更新されないことがあったため
         */
        imgCacheClear(path){
            if(path.substr(0,4) == 'blob') return path;
            let date = new Date();
            // パスに日時を埋め込むことでキャッシュを読み込ませないようにしている
            return path + '?' + date
        }
    },
};
</script>