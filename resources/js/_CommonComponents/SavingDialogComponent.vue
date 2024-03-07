<template>
    <v-dialog v-model="dialog" persistent max-width="500px" transition="dialog-transition">
        <div class="save-dialog__wrap pa-10">
            <v-progress-circular :size="50" :width="7" color="#13994f" indeterminate></v-progress-circular>
            <div class="pt-5 save-dialog__text">保存処理中・・・少々お待ちください。</div>
        </div>
    </v-dialog>
</template>

<script>
export default {
    props: {
        value: {
            type: Boolean,
            default: false
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
    }
};
</script>
<style scoped>
.save-dialog__wrap {
    background-color: white;
    text-align: center;
}
.save-dialog__text {
    color: #13994f;
    font-size: 1.5em;
    font-weight: bold;
}
</style>