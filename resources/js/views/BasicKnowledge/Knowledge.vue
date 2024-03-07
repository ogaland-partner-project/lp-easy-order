<template>
    <div>
        <div v-for="(knowledgeModel, index) in knowledgeModels" :key="index" class="d-flex" ref="dFlex">
            <div
                class="knowledge_add_block"
                v-for="(cols, cIndex) in knowledgeModel.details"
                :key="cIndex"
            >
                <div class="knowledge_del_block">
                    <v-btn
                        @click="onDeleteCol(cIndex)"
                        fab
                        x-small
                        depressed
                        color="rgb(187,187,187)"
                        v-if="!getCheckEdit"
                    >
                        <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                    </v-btn>
                </div>
                <template v-for="(col, ccIndex) in cols">
                    <div :key="ccIndex">
                        <div class="solo_box">
                            <v-textarea
                                class="solo_box_title outlined_field"
                                v-model="col.title"
                                auto-grow
                                dense
                                hide-details="auto"
                                rows="1"
                            ></v-textarea>
                            <v-textarea
                                class="solo_box_textaria outlined_field"
                                v-model="col.detail"
                                outlined
                                auto-grow
                                dense
                                hide-details="auto"
                                rows="3"
                            ></v-textarea>
                            <div class="solo_box_delbtn">
                                <v-btn
                                    @click="onDeleteRow(cIndex,ccIndex)"
                                    fab
                                    x-small
                                    depressed
                                    color="rgb(110,110,110)"
                                    v-if="!getCheckEdit"
                                >
                                    <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                                </v-btn>
                            </div>
                        </div>
                        <v-hover v-slot="{ hover }" style="width:100%; height:10px;">
                            <div style="width:100%; height:10px;" @click="onAddRow(cIndex,ccIndex)">
                                <div v-if="hover && !getCheckEdit" class="blockadd_gyou">&nbsp;</div>
                            </div>
                        </v-hover>
                    </div>
                </template>
            </div>
        </div>
        <v-dialog max-width="500" scrollable v-model="rowDeleteDialog">
        <v-card class="dialog_box">
            <v-card-title class="card_title">
                <v-icon size="30px" class="pr-4">fa-solid fa-trash-can</v-icon>
                <span style="width:85%;">商品/原材料の削除</span>
                <v-icon @click="rowDeleteDialog = false" medium>fa-times</v-icon>
            </v-card-title>
            <v-card-text>
                <span class="alert_messege mb-4">本当に削除しますか？</span>
                <div class="select_btn_box">
                    <div class="dialog_item_edit_btn">
                        <v-btn @click="fnDeleteCol">削除</v-btn>
                    </div>
                    <div class="dialog_item_clear_btn">
                        <v-btn @click="rowDeleteDialog = false">キャンセル</v-btn>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
    props: {
        knowledgeModels: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            deleteTarget:null,
            rowDeleteDialog:false
        };
    },

    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },

    methods: {
        // 詳細部を1列分ごっそり削除
        onDeleteCol(cIndex) {
            this.deleteTarget = cIndex;
            this.rowDeleteDialog = true;
        },
        fnDeleteCol(){
            this.knowledgeModels[0].details.splice(this.deleteTarget, 1);
            this.deleteTarget = null;
            this.rowDeleteDialog = false;
        },

        // 詳細部の1行分を削除
        onDeleteRow(cIndex, ccIndex) {
            this.knowledgeModels[0].details[cIndex].splice(ccIndex, 1);
        },

        // 詳細部の1行分を追加
        onAddRow(cIndex, ccIndex) {
            const length = this.knowledgeModels[0].details[cIndex].length + 1;
            this.knowledgeModels[0].details[cIndex].splice(ccIndex + 1, 0,
                {
                    title: "キーワードを入力",
                    detail: "",
                    sort_order: length,
                }
            );
        }
    }
};
</script>

<style>
</style>