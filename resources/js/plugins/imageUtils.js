import imageCompression from "browser-image-compression";

export default {
// アップロードされた画像ファイルを取得
async getCompressImageFileAsync(file, filesize = 1000) {
    const options = {
    maxSizeMB: 1, // 最大ファイルサイズ
    maxWidthOrHeight: filesize // 最大画像幅もしくは高さ
    };
    try {
    // 圧縮画像の生成
    return await imageCompression(file, options);
    } catch (error) {
    window.console.error("getCompressImageFileAsync is error", error);
    throw error;
    }
},
// プレビュー表示用のdataurlを取得
async getDataUrlFromFile(file) {
    try {
    return await imageCompression.getDataUrlFromFile(file);
    } catch (error) {
    window.console.error("getDataUrlFromFile is error", error);
    throw error;
    }
},

// 画像のサイズを取得
async getImageSize(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
        var return_size;
        const size = {
            width: img.naturalWidth,
            height: img.naturalHeight,
        };
        URL.revokeObjectURL(img.src);
        if(size.width > size.height){
            if(size.width > 1000){
            return_size = 1000;
            }else{
            return_size = size.width;
            }
        }else if(size.width < size.height){
            if(size.height > 800){
            return_size = 800;
            }else{
            return_size = size.height;
            }
        }else{
            return_size = size.width;
        }
        resolve(return_size);
        };
        img.onerror = (error) => {
        reject(-1);
        };
        img.src = URL.createObjectURL(file);
    });
}
};