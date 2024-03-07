<template>
    <v-dialog max-width="500" scrollable v-model="dialog">
        <v-card class="dialog_box">
            <v-card-title class="card_title">
                <v-icon size="30px" class="pr-4">fa-solid fa-copy</v-icon>
                <span style="width:85%;">構成コピー</span>
                <v-icon @click="dialog = false" medium>fa-times</v-icon>
            </v-card-title>
            <v-card-text>
                <span class="alert_messege mb-4">
                    選択している構成データをコピーし、新規作成。
                    <div>[ID]: {{ model.id }}</div>
                    <div>[商品名]： {{ model.product_name }}</div>
                </span>
                <div class="select_btn_box">
                    <div class="dialog_item_edit_btn">
                        <v-btn @click="onCopy">コピー</v-btn>
                    </div>
                    <div class="dialog_item_clear_btn">
                        <v-btn class="cancel_btn_color" @click="dialog = false">キャンセル</v-btn>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        value: {
            type: Boolean,
            default: false
        },
        model: {
            type: Object,
            default: () => {}
        }
    },

    computed: {
        dialog: {
            get() {
                return this.value;
            },
            set(newVal) {
                this.$emit("input", newVal);
            }
        }
    },

    methods: {
        onCopy: async function() {
            const params = {
                lp_order_id: 0,
                other_lp_order_id: this.model.id
            };

            const result = await this.$axios({
                method: "POST",
                url: "/home/copy",
                data: params
            });
            this.dialog = false;
            this.$emit("close");
        }
    }
};
</script>

<style>
</style>
