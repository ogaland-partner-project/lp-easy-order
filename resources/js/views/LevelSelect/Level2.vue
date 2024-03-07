<template>
    <div>
        <div class="level-two-aria">
            <v-textarea class="outlined_field" label="どういう売り方？訴求？・目的" v-model="levelModel.purpose" outlined auto-grow></v-textarea>

            <v-textarea class="outlined_field" label="ポイント・特徴は？" v-model="levelModel.point1" outlined auto-grow></v-textarea>

            <p class="ml-2" style="color:#A4A4A4;">入れたいブロック</p>
            <draggable tag="div" v-model="levelModel.t_level_select_lp_blocks" :options="{animation:300}">
                <div
                    v-for="(block,index) in levelModel.t_level_select_lp_blocks"
                    :key="index"
                    class="iretai_block"
                >
                    <v-textarea
                        class="outlined_field"
                        v-model="block.block_detail"
                        outlined
                        auto-grow
                        hide-details="auto"
                        rows="4"
                    ></v-textarea>
                    <div class="iretai_del_btn">
                        <v-btn
                            @click="deleteBlock(index)"
                            class="iretai_del_btn"
                            fab
                            depressed
                            color="rgb(110,110,110)"
                            v-if="!getCheckEdit"
                        >
                            <v-icon color="white" style="font-size:14px;">fa-solid fa-xmark</v-icon>
                        </v-btn>
                    </div>
                    <v-hover v-if="!getCheckEdit" v-slot="{ hover }">
                        <div style="height:10px; width:100%; position:absolute; bottom:-15px;">
                            <div v-if="hover" class="blockadd_gyou level-two-blockadd" @click="blcokAdd(index)">&nbsp;</div>
                        </div>
                    </v-hover>
                </div>
            </draggable>
        </div>
    </div>
</template>
<script>
import draggable from "vuedraggable";
import { mapGetters } from "vuex";
export default {
    components: {
        draggable: draggable
    },

    props: {
        // 親から元データ引継ぎ
        levelModel: {
            type: Object,
            default: () => ({})
        }
    },

    data() {
        return {
        };
    },
    computed:{
        ...mapGetters("common", ["getCheckEdit"]),
    },
    methods: {
        blcokAdd(index) {
            let block = {
                id: null,
                block_detail: ""
            }
            this.levelModel.t_level_select_lp_blocks.splice(index+1,0,block);
        },
        deleteBlock(index) {
            this.levelModel.t_level_select_lp_blocks.splice(index, 1);
        }
    }
};
</script>