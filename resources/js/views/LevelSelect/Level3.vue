<template>
    <div>
        <div class="level-three-aria">
            <v-textarea
                class="outlined_field"
                label="どういう売り方？訴求？・目的"
                v-model="levelModel.purpose"
                outlined
                auto-grow
            ></v-textarea>
            <div class="level-aria_info">
                構成案シートのLPの流れをご記入ください。<br>
                詳細を入れたい場合は、ご記入ください。
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        // 親から元データ引継ぎ
        levelModel: {
            type: Object,
            'default': () => ({})
        }
    },
}
</script>