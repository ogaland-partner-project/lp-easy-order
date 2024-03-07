<template>
    <v-dialog v-model="dialogFlag" persistent max-width="800px">
        <v-card>
            <v-card-title>
                <span class="headline">サンプル {{ editMode }}</span>
            </v-card-title>

            <v-form ref="form" v-model="valid" v-bind:lazy-validation="lazy">
                <v-card-text>
                    <v-container>
                        <v-text-field
                            dense
                            disabled
                            class="w-25"
                            v-model="selectId"
                            label="ID"
                            placeholder="新規"
                        ></v-text-field>
                        <v-text-field
                            dense
                            v-bind:disabled="CodeLock"
                            v-bind:counter="8"
                            v-bind:rules="codeRules"
                            class="w-25"
                            v-model="editItem.code"
                            label="code"
                        ></v-text-field>
                        <v-text-field
                            dense
                            class="w-80"
                            required
                            v-bind:counter="50"
                            v-bind:rules="GetRequire(50)"
                            v-model="editItem.order_name"
                            label="得意先名"
                        ></v-text-field>
                        <v-text-field
                            dense
                            class="w-60"
                            required
                            v-bind:counter="80"
                            v-bind:rules="GetRequire(80)"
                            v-model="editItem.order_kana"
                            label="フリガナ"
                        ></v-text-field>
                        <v-text-field
                            dense
                            class="w-50"
                            required
                            v-model="editItem.phone_number"
                            label="電話番号"
                        ></v-text-field>
                        <v-text-field
                            dense
                            class="w-80"
                            v-bind:rules="emailRules"
                            v-model="editItem.order_email"
                            label="メールアドレス"
                        ></v-text-field>
                    </v-container>
                </v-card-text>
            </v-form>

            <v-card-actions>
                <!-- 
                <v-switch
                    v-model="valid"
                    class="ma-4"
                    label="Valid"
                    readonly
                ></v-switch>
 -->
                <v-btn
                    v-show="CodeLock"
                    class="error"
                    text
                    v-on:click="DeleteData"
                    >削除</v-btn
                >
                <v-spacer></v-spacer>
                <v-btn
                    v-bind:disabled="!valid"
                    class="primary"
                    text
                    v-on:click="SaveData"
                    >登 録</v-btn
                >
                <v-btn class="info" text v-on:click="CloseDialog">閉じる</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { $WebAPI } from "@/_CommonJs/WebAPI.js";
import { $WebMsg } from "@/_CommonJs/WebMsg.js";

export default {
    props: {
        selectId: { type: String, default: "" },
        dialogFlag: { type: Boolean, default: false }
    },
    data() {
        return {
            valid: true,
            lazy: false,
            editMode: "【新規】",
            CodeLock: false,
            editItem: {
                id: 0,
                code: "0",
                order_name: "",
                order_kana: "",
                order_email: "",
                phone_number: ""
            },
            codeRules: [
                v => !!v || "Code is required",
                v => /^[0-9]+(\.[0-9]+)?$/.test(v) || "Code must be Numeric"
            ],
            emailRules: [
                v => !!v || "E-mail is required",
                v => /.+@.+\..+/.test(v) || "E-mail must be valid"
            ]
        };
    },
    watch: {
        dialogFlag: {
            immediate: true,
            handler: function() {
                if (this.dialogFlag) {
                    this.InitOrder();
                }
            }
        }
    },
    methods: {
        InitData: function() {
            this.editItem.id = "";
            this.editItem.code = 0;
            this.editItem.order_name = "";
            this.editItem.order_kana = "";
            this.editItem.order_email = "";
            this.editItem.phone_number = "";
            this.$refs.form.reset();
        },
        InitOrder: async function() {
            try {
                if (this.selectId == "") {
                    this.editMode = "【新規】";
                    this.CodeLock = false;
                    this.InitData();
                } else {
                    const perPage = this.itemsPerPage;
                    const result = await $WebAPI.GetAxios(
                        "/api/sample/GetDataOnce/" + String(this.selectId)
                    );

                    this.editItem = result.data.rows[0];
                    this.editMode = "【編集】";
                    this.CodeLock = true;
                }
            } catch (error) {
                // 例外処理
                const { status, statusText } = error.response;
                console.log(`Error! HTTP Status: ${status} ${statusText}`);
            }
        },
        GetRequire: function(size) {
            let ans = [
                v => !!v || "Name is required",
                v =>
                    (v && v.length <= size) ||
                    "Name must be less than " + size + " characters"
            ];

            return ans;
        },

        SaveData: async function() {
            // 追加・更新
            try {
                const perPage = this.itemsPerPage;
                const jsonData = JSON.stringify(this.editItem);
                const result = await $WebAPI.PostAxios(
                    "/api/sample/SaveData",
                    jsonData
                );

                if (result.data.ErrMessage == "") {
                    await $WebMsg.InfSuccess("登録確認", "登録しました。");
                    let jText = JSON.stringify(this.editItem);
                    this.$emit("order-commit", jText);
                    this.InitData();
                } else {
                    const res = await $WebMsg.InfWarning(
                        "登録ＮＧ",
                        result.data.ErrMessage
                    );
                }
            } catch (error) {
                // 例外処理
                const { status, statusText } = error.response;
                console.log(`Error! HTTP Status: ${status} ${statusText}`);
            }
        },
        DeleteData: async function() {
            // 削除処理

            let msgRes = await $WebMsg.OkCancel("", "削除しますか。");

            if (msgRes == 0) {
                //削除中断
                return;
            }

            try {
                const perPage = this.itemsPerPage;
                const jsonData = JSON.stringify(this.editItem);
                const result = await $WebAPI.PostAxios(
                    "/api/sample/DeleteData",
                    jsonData
                );

                if (result.data.ErrMessage == "") {
                    await $WebMsg.InfSuccess("", "削除しました。");
                    let jText = JSON.stringify(this.editItem);
                    this.$emit("delete-commit", jText);
                    this.InitData();
                } else {
                    const res = await $WebMsg.InfWarning(
                        "削除ＮＧ",
                        result.data.ErrMessage
                    );
                }
            } catch (error) {
                // 例外処理
                const { status, statusText } = error.response;
                console.log(`Error! HTTP Status: ${status} ${statusText}`);
            }
        },
        CloseDialog: function() {
            this.$emit("order-cancel");
            this.InitData();
        }
    }
};
</script>

<style scoped>
.v-row {
    margin: 0px;
}
</style>
