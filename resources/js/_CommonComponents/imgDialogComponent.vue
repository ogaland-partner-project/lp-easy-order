<template>
    <v-dialog
        v-model="imgDialog"
        width="60rem"
    >
        <v-carousel
            v-model="img_count"
            height="90% !important"
        >
            <v-carousel-item
                v-for="(image, i) in imgDialogData"
                :key="i"
                :src="image.image_path"
            >
            </v-carousel-item>
        </v-carousel>
    </v-dialog>
</template>

<script>
export default {
    data(){
        return {
            imgDialog:false,
            imgDialogData:[],
            img_count:0,
        }
    },
    methods:{
        open(image_paths,index){
            this.imgDialogData = image_paths.filter((image)=>{
                return (image.image_path)
            })
            this.img_count = index;
            this.imgDialog = true;
        }
    }
}
</script>

<style>

</style>