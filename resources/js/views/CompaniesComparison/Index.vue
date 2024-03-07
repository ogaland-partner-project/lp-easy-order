<template>
        <div id="comparison_main">
            <div class="koumoku_icontitle">項目
                <v-tooltip right color="rgb(175,154,227)">
                    <template v-slot:activator="{on,attrs}">
                        <v-icon v-bind="attrs"  v-on="on" color="rgb(175,154,227)" class="ml-1 mb-1">fa-solid fa-comment-dots</v-icon>
                    </template>
                    <span>セルのクリックで入力・編集・削除ができます。</span>
                </v-tooltip>
            </div>
            <!-- テーブルコンポーネント -->
            <comparison-table
                ref="comparison_table"
                parent="companies"
            >
            </comparison-table>
            <com-clear ref="clear" @delete_action="fn_clear"></com-clear>
        </div>
</template>

<script>
import { mapActions } from "vuex";
import ComparisonTable from '@/views/ComparisonInsert/ComparisonTable.vue';
export default {
    components: {
    'comparison-table':ComparisonTable,
    },
    data() {
        return {
        };
    },
    methods: {
        ...mapActions("common", ["setSelectionMenu"]),

        // 動きの処理---------------------------
        // 全クリア確認ダイアログ表示
        clear(){
            this.$refs.clear.open();
        },
        // 全クリア処理
        fn_clear(){
            this.$refs.comparison_table.create();
        },
        // サイドメニューの保存ボタンからの保存処理
        save(){
            return this.$refs.comparison_table.save();
        },

        search(){
            this.$refs.comparison_table.search();
        }
    },
};

</script>