<template>
    <div id="comparison_main">
        <!-- ヘッダー編集ダイアログ -->
        <v-dialog max-width="500" scrollable v-model="headerEditDialog">
            <v-form v-model="valid">
                <v-card class="dialog_box">
                    <v-card-title class="card_title_ComparisonInsert">
                        <v-icon size="30px" class="pr-4">mdi-table-arrow-right</v-icon>
                        <span style="width:85%;">項目の編集</span>
                        <v-icon @click="headerEditDialog=false" medium>fa-times</v-icon>
                    </v-card-title>
                    <v-card-text>
                        <div class="mt-5">
                            <v-text-field
                                label="タイトル"
                                v-model="targetHeader.header_name"
                                outlined
                                clearable
                                dense
                                hide-details="auto"
                                :rules="[rules.required]"
                            ></v-text-field>
                        </div>
                        <div style="display:flex;" class="mb-5">
                            <v-checkbox
                                class="mr-3"
                                v-model="targetHeader.comparison_insert_flag"
                                label="他社比較入力"
                                hide-details
                                :disabled="parent=='insert'"
                                :value="1"
                            ></v-checkbox>
                            <v-checkbox
                                v-model="targetHeader.companies_comparison_flag"
                                label="他社構成比較"
                                :disabled="parent=='companies'"
                                hide-details
                                :value="1"
                            ></v-checkbox>
                        </div>
                        <v-radio-group v-model="targetHeader.header_type" hide-details class="selectbox_ComparisonInsert">
                            <v-radio value="text" label="テキスト"></v-radio>
                            <v-radio value="url" label="url"></v-radio>
                            <v-radio value="image" label="画像"></v-radio>
                            <v-radio value="calculation" label="計算"></v-radio>
                        </v-radio-group>
                        <div class="selectboxsub_ComparisonInsert" v-if="targetHeader.header_type == 'calculation'">
                            <v-radio-group
                                v-model="targetHeader.calculation_type"
                                :rules="[rules.required]"
                            >
                                <v-radio value="sum" label="合計"></v-radio>
                                <v-radio value="subtraction" label="引き算"></v-radio>
                                <v-radio value="average" label="平均"></v-radio>
                                <v-radio value="division" label="割り算"></v-radio>
                            </v-radio-group>
                            <!--　▼▼合計選択時▼▼　-->
                            <div v-if="targetHeader.calculation_type == 'sum' || targetHeader.calculation_type == 'average'">
                                <v-autocomplete
                                    label="対象の項目を選択"
                                    multiple
                                    outlined
                                    dense
                                    hide-details="auto"
                                    v-model="targetHeader.calculation_row"
                                    :items="selectHeaders()"
                                    item-text="header_name"
                                    item-value="id"
                                    :rules="[rules.requiredSelectItems]"
                                ></v-autocomplete>
                            </div>
                            <!--　▼▼引き算・割り算選択時▼▼　-->
                            <div v-if="targetHeader.calculation_type == 'subtraction' || targetHeader.calculation_type == 'division'" style="display:flex; align-items: center;">
                                <v-autocomplete
                                    style="width:40%"
                                    label="対象の項目を選択"
                                    hide-details="auto"
                                    outlined
                                    dense
                                    v-model="targetHeader.calculation_row[0]"
                                    :items="selectHeaders()"
                                    item-text="header_name"
                                    item-value="id"
                                    :rules="[rules.required]"
                                ></v-autocomplete>
                                <span v-if="targetHeader.calculation_type == 'subtraction'" class="cal_mark">－</span>
                                <span v-if="targetHeader.calculation_type == 'division'" class="cal_mark">÷</span>
                                <v-autocomplete
                                    style="width:40%"
                                    label="対象の項目を選択"
                                    hide-details="auto"
                                    outlined
                                    dense
                                    v-model="targetHeader.calculation_row[1]"
                                    :items="selectHeaders()"
                                    item-text="header_name"
                                    item-value="id"
                                    :rules="[rules.required]"
                                ></v-autocomplete>
                            </div>
                        </div>
                        <div class="select_btn_box">
                            <div class="dialog_item_edit_btn color__ComparisonInsert">
                                <v-btn @click="headerEditDialog=false" :disabled="!valid">更新</v-btn>
                            </div>
                            <div v-if="!getCheckEdit" class="dialog_item_delete_btn">
                                <v-btn @click="headerDelete()">この項目を削除</v-btn>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-form>
        </v-dialog>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
    props: {
        headers: {
            type: Array,
            default: () => []
        },
        targetHeader: {
            type: Object,
            default: () => []
        },
        parent:{
            type: String,
            default: () => ''
        }
    },
    data() {
        return {
            lpOrderId:1,
            headerEditDialog:false,
            menu_position: {
                left: 0,
                top: 0,
                targetRow:null,
                targetColmun:null,
                color:"#FFFFFF",
            },
            valid: false,
            rules: {
                //バリデーション
                required: (v) => !!v || "必須項目です。",
                requiredSelectItems: (v) => !!v.length || "必須項目です。",
            },
        };
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },
    methods: {
        // 動き------------------------------------------------------
        // 計算対象のヘッダーを絞る
        selectHeaders(){
            let targetHeaders = this.headers.filter((header)=>{
                if(this.parent == 'insert'){
                    return header.comparison_insert_flag == 1;
                }
                return header.companies_comparison_flag == 1;
            })
            return targetHeaders;
        },

        // ヘッダー編集ダイアログ表示処理
        open(){
            this.headerEditDialog = true;
        },

        close(){
            this.headerEditDialog = false;
        },

        // ヘッダー削除処理
        headerDelete(){
            this.$emit('headerDelete');
        }

    },
};

</script>
