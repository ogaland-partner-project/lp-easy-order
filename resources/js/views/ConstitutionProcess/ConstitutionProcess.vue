<template>
    <div>
        <div class="seat_control_btn">
        </div>

        <div id="procedure_main_aria">
            <div class="procedure_title_aria">
                <ul>
                    <li>商品の軸を決めよう</li>
                    <li>構成の原型を決めよう</li>
                    <li>全体構成を決めよう</li>
                    <li>修正後</li>
                </ul>
            </div>
            <div class="procedure_line_up">
                <div class="first_block">
                    <div class="box_title">Q.商品コンセプトは？</div>
                    <v-textarea
                    v-model="concept_word"
                    outlined
                    auto-grow
                    dense
                    hide-details="auto"
                    rows="5"
                    color="orange orange-darken-4"
                    class="mb-5 outlined_field"
                    ></v-textarea>
                    <div class="box_title">Q.キャッチなど（メモ）</div>
                    <div class="tejun_input_box" v-for="(catchDetail,n) in constitution_catchphrase_list" :key="n">
                        <v-textarea
                        v-model="catchDetail.catchphrase"
                        dense
                        placeholder="キャッチ"
                        auto-grow
                        rows="1"
                        hide-details="auto"
                        class="mb-3"
                        clearable
                        ></v-textarea>
                        <div class="tejun_input_box_delbtn">
                            <v-btn v-if="!getCheckEdit" @click="deleteBlock(n,constitution_catchphrase_list,false)" fab x-small depressed color="rgb(110,110,110)"><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                        </div>
                        <v-hover v-slot="{ hover }" style="width:100%; height:10px;">
                            <div style="width:100%; height:10px;" @click="addBlock(n,constitution_catchphrase_list,false)">
                                <div v-if="hover && !getCheckEdit" class="blockadd_gyou">&nbsp;</div>
                            </div>
                        </v-hover>
                    </div>
                </div>
                <div class="second_block">
                    <div class="box_title">Q.どんなブロックを入れる？</div>
                    <draggable
                        :disabled="getCheckEdit"
                        :group="{ name: 'block', pull: 'clone', put: true }"
                        v-model="constitution_how_blocks_list"
                        @change="howDrop"
                        handle=".handle"
                        :options="{animation:300}"
                        :force-fallback="true"
                        :scroll-sensitivity="200"
                    >
                        <div class="tejun_input_box" v-for="(howDetail,n) in constitution_how_blocks_list" :key="n">
                            <div class="mb-1" style="display:flex; width: 100%;">
                                <v-textarea label="キーワードを入力"
                                v-model="howDetail.block_detail"
                                dense
                                solo
                                auto-grow
                                rows="1"
                                flat
                                color="info"
                                background-color="rgba(153,153,153,0.1)"
                                hide-details="auto"
                                clearable
                                >
                                </v-textarea>
                                <div class="drag-block-handle">
                                    <v-icon  class="handle">fas fa-bars</v-icon>
                                </div>
                            </div>
                            <div class="tejun_input_box_delbtn">
                                <v-btn v-if="!getCheckEdit" @click="deleteBlock(n,constitution_how_blocks_list,'how')" fab x-small depressed color="rgb(110,110,110)"><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                            </div>
                            <v-hover v-slot="{ hover }" style="width:100%; height:10px;">
                                <div style="width:100%; height:10px;" @click="addBlock(n,constitution_how_blocks_list,'how')">
                                    <div v-if="hover && !getCheckEdit" class="blockadd_gyou">&nbsp;</div>
                                </div>
                            </v-hover>
                        </div>
                    </draggable>
                </div>
                <div class="third_block">
                    <div class="box_title">Q.ブロックを並べよう</div>
                        <draggable
                            :disabled="getCheckEdit"
                            :group="{ name: 'block', pull: 'clone', put: true }"
                            v-model="constitution_blocks_list"
                            @change="blockDrop"
                            handle=".handle"
                            :options="{animation:300}"
                            :force-fallback="true"
                            :scroll-sensitivity="200"
                        >
                            <div class="tejun_input_box" v-for="(block,n) in constitution_blocks_list" :key="n">
                                <div class="mb-1" style="display:flex; align-items:center;">
                                    <span class="pr-1" style="width:7%;">{{n+1}}</span>
                                    <div style="display:flex; width: 100%;">
                                        <v-textarea label="キーワードを入力"
                                        v-model="block.block_detail"
                                        dense
                                        solo
                                        auto-grow
                                        rows="1"
                                        flat
                                        color="info"
                                        background-color="rgba(153,153,153,0.1)"
                                        hide-details="auto"
                                        clearable
                                        ></v-textarea>
                                        <div class="drag-block-handle">
                                            <v-icon  class="handle">fas fa-bars</v-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="tejun_input_box_delbtn">
                                    <v-btn v-if="!getCheckEdit" @click="deleteBlock(n,constitution_blocks_list,'block')" fab x-small depressed color="rgb(110,110,110)"><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                                </div>
                                <v-hover v-slot="{ hover }" style="width:100%; height:10px;">
                                    <div style="width:100%; height:10px;" @click="addBlock(n,constitution_blocks_list,'block')">
                                        <div v-if="hover && !getCheckEdit" class="blockadd_gyou">&nbsp;</div>
                                    </div>
                                </v-hover>
                            </div>
                        </draggable>
                </div>
                <div class="last_block">
                    <div class="box_title">Q.ブロックを並べよう</div>
                        <draggable
                            :disabled="getCheckEdit"
                            :group="{ name: 'block', pull: 'clone', put: true }"
                            v-model="constitution_fix_blocks_list"
                            @change="fixDrop" handle=".handle"
                            :options="{animation:300}"
                            :force-fallback="true"
                            :scroll-sensitivity="200"
                        >
                            <div class="tejun_input_box" v-for="(fix_block,n) in constitution_fix_blocks_list" :key="n">
                                <div class="mb-1" style="display:flex; align-items:center;">
                                    <span class="pr-1" style="width:7%;">{{n+1}}</span>
                                    <div style="display:flex; width: 100%;">
                                        <v-textarea label="キーワードを入力"
                                        v-model="fix_block.block_detail"
                                        dense
                                        solo
                                        flat
                                        auto-grow
                                        rows="1"
                                        color="info"
                                        background-color="rgba(153,153,153,0.1)"
                                        hide-details="auto"
                                        clearable
                                        ></v-textarea>
                                        <div class="drag-block-handle">
                                            <v-icon  class="handle">fas fa-bars</v-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="tejun_input_box_delbtn">
                                    <v-btn v-if="!getCheckEdit" @click="deleteBlock(n,constitution_fix_blocks_list,'fix')" fab x-small depressed color="rgb(110,110,110)"><v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon></v-btn>
                                </div>
                                <v-hover v-slot="{ hover }" style="width:100%; height:10px;">
                                    <div style="width:100%; height:10px;" @click="addBlock(n,constitution_fix_blocks_list,'fix')">
                                        <div v-if="hover && !getCheckEdit" class="blockadd_gyou">&nbsp;</div>
                                    </div>
                                </v-hover>
                            </div>
                        </draggable>
                </div>
            </div>
        </div>
        <saving-dialog v-model="saveDialog" />
        <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import { mapGetters } from "vuex";
export default {
    components: {
    'draggable': draggable,
    },
    data(){
        return{
            lpOrderId:1,
            concept_word:"",
            constitution_catchphrase_list:[],
            constitution_how_blocks_list:[],
            constitution_blocks_list:[],
            constitution_fix_blocks_list:[],
            search_flg:false,
            saveDialog: false,
        }
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },
    created(){
        this.lpOrderId = Number(this.$route.params.id);
        this.search()
    },
    methods:{
        addBlock(index,blocks,type){
            let block = { id:null };
            if(!type){
                block.catchphrase = ""
            }else{
                block.block_detail = "";
                block.type = type
            }
            blocks.splice(index+1,0,block);
        },
        deleteBlock(index,blocks,type){
            blocks.splice(index,1);
            if(blocks.length == 0){
                this.addBlock(0,blocks,type);
            }
        },
        clear(){
            this.$refs.clear.open();
        },
        howDrop(e){
            if(e.added){
                // ブロックコピーの際、コピーした要素が参照渡しの為
                this.constitution_how_blocks_list = JSON.parse(JSON.stringify(this.constitution_how_blocks_list));
                let targetBlock = this.constitution_how_blocks_list.find((block)=>{
                    return block.type !== 'how';
                })
                targetBlock.id = null;
                targetBlock.type = 'how';
            }
        },
        blockDrop(e){
            if(e.added){
                // ブロックコピーの際、コピーした要素が参照渡しの為
                this.constitution_blocks_list = JSON.parse(JSON.stringify(this.constitution_blocks_list));
                let targetBlock = this.constitution_blocks_list.find((block)=>{
                    return block.type !== 'block';
                })
                targetBlock.id = null;
                targetBlock.type = 'block';
            }
        },
        fixDrop(e){
            if(e.added){
                // ブロックコピーの際、コピーした要素が参照渡しの為
                this.constitution_fix_blocks_list = JSON.parse(JSON.stringify(this.constitution_fix_blocks_list));
                let targetBlock = this.constitution_fix_blocks_list.find((block)=>{
                    return block.type !== 'fix';
                })
                targetBlock.id = null;
                targetBlock.type = 'fix';
            }
        },
        // 検索
        search:async function(){
            const res = await this.$axios({
                method: "GET",
                url: "constitution_process/"+this.lpOrderId,
            });
            this.concept_word = res.data.dataArray.concept_word;
            this.constitution_catchphrase_list = res.data.dataArray.constitution_catchphrase_list;
            this.constitution_how_blocks_list = res.data.dataArray.constitution_how_blocks_list;
            this.constitution_blocks_list = res.data.dataArray.constitution_blocks_list;
            this.constitution_fix_blocks_list = res.data.dataArray.constitution_fix_blocks_list;
            this.$nextTick(()=>{
                this.search_flg = true;
            })
        },
        // 登録・更新
        save:async function(){
            // 検索前に画面遷移されると空データを登録してしまう為。
            if(!this.search_flg) return false;
            try{
                this.saveDialog = true;
                await this.$axios({
                    method: "PUT",
                    url: "constitution_process/"+this.lpOrderId,
                    data:{
                        concept_word:this.concept_word,
                        constitution_catchphrase_list:this.constitution_catchphrase_list,
                        constitution_how_blocks_list:this.constitution_how_blocks_list,
                        constitution_blocks_list:this.constitution_blocks_list,
                        constitution_fix_blocks_list:this.constitution_fix_blocks_list
                    }
                }).then(()=>{
                    this.saveDialog = false;
                    this.search()
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
            this.concept_word = "";
            this.constitution_catchphrase_list = [];
            this.constitution_how_blocks_list = [];
            this.constitution_blocks_list = [];
            this.constitution_fix_blocks_list = [];
            this.save();
        },
        copy:async function(){
            await this.$axios({
                method: "POST",
                url: "constitution_process/copy",
                data:{
                    lp_order_id:2,
                    other_lp_order_id:1,
                }
            })
        },
    },
}
</script>