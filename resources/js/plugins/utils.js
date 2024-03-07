export const Utils = {
    logging: (data) => {},

    /**
     * 指定のオブジェクトから、指定のパスまで掘り下げて値を取得する
     *
     * @param {*} object オブジェクト
     * @param {string} path パスの文字列。引数 object のトップレベルプロパティから
     *                      ピリオド区切りで記す。配列の添字もピリオドで記す。
     *                      ex. 'items.0.options.name'
     * @return {*} 連想配列の値。取得できなかった場合は undefined が返される
     */
    getExistKey: (object, path) => {
        let lookup = Object.assign({}, object);
        const keys = `${path}`.split(".");
        const length = keys.length;
        for (let index = 0; index < length; index++) {
            if (lookup[keys[index]] == null) {
                return index === length - 1 ? lookup[keys[index]] : undefined;
            }
            lookup = lookup[keys[index]];
        }
        return lookup;
    },

    /**
     * オブジェクトが空か判定
     * @param {*} object オブジェクト
     * @returns boolean
     */
    isObjectEmpty: (object) => {
        return !Object.keys(object).length;
    },

    /**
     * 複数の特定要素のうちひとつでも当てはまったら true を返す関数
     *
     * @param array arr 比較元の配列
     * @param string target 要素内の文字列　この文字列を配列内に1つでも含んでいる要素があるか判定
     * @returns boolean
     */
    isIncludes : (arr, target) => {
        return arr.some(el => target.includes(el))
    }
};