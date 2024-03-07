/****************************************************
  メッセージ確認ダイアログClass【SweetAlert】
    作成日：2019.10.10
    作成者：泉
    バージョン：0.01
 ***************************************************/
import Swal from 'sweetalert2'

class WebMsg {


  /**
   *
   * @param {タイトル変数} title
   * @param {メッセージ変数} msg
   */
  InfOk(title, msg) {
    return new Promise(function (resolve, reject) {
      Swal.fire({
        title: title,
        html: msg,
        icon: 'info',
        showCancelButton: false,
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result.value) {
          resolve(1)
          window.console.log(reject)
        } else {
          resolve(0)
          window.console.log(reject)
        }
      })
    })
  }

  /**
    *
    * @param {タイトル変数} title
    * @param {メッセージ変数} msg
    */
  InfSuccess(title, msg) {
    return new Promise(function (resolve, reject) {
      Swal.fire({
        title: title,
        html: msg,
        icon: 'success',
        showCancelButton: false,
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result.value) {
          resolve(1)
          window.console.log(reject)
        } else {
          resolve(0)
          window.console.log(reject)
        }
      })
    })
  }

  /**
   *
   * @param {タイトル変数} title
   * @param {メッセージ変数} msg
   */
  async InfWarning(title, msg) {
    return new Promise(function (resolve, reject) {
      Swal.fire({
        title: title,
        html: msg,
        icon: 'warning',
        showCancelButton: false,
        confirmButtonText: 'OK'
      }).then((result) => {
        if (result.value) {
          resolve(1)
          window.console.log(reject)
        } else {
          resolve(0)
          window.console.log(reject)
        }
      })
    })
  }

  /**
   * @param {タイトル変数} title
   * @param {メッセージ変数} msg
   */
  OkCancel(title, msg) {
    return new Promise(function (resolve, reject) {
      Swal.fire({
        title: title,
        html: msg,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel'
      }).then(function (result) {
        if (result.value) {
          resolve(1)
          window.console.log(reject)
        } else {
          resolve(0)
          window.console.log(reject)
        }
      })
    })
  }
}

export let $WebMsg = new WebMsg()
