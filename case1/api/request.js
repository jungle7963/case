const { baseUrl } = require('./env').dev

var request = function(url, data = {}, callback=''){
    let fullUrl = `${baseUrl}${url}`
    let token = wx.getStorageSync('token') ? wx.getStorageSync('token') : ''
    if (url != 'data/api.auth.Tasks/getTasks' && url != 'data/api.Tasks/getTasks') {
        wx.showLoading({
            title: '加载中',
        })
    }
    return new Promise((resolve,reject)=>{
        wx.request({
            url: fullUrl,
            method : 'POST',
            data,
            header: {
                'content-type': 'application/json', // 默认值
                'api-token': token,
                'api-name' : 'wxapp'
            },
            success(res){
                if(callback){
                    callback(res.data.data);
                    return;
                }
                if (res.data.code == 1) {
                    resolve(res.data.data)
                }else if(res.data.code == 401){
                    wx.login({
                        success(res){
                            wx.request({
                                url: `${baseUrl}data/api.wxapp/session`, 
                                method : 'POST',
                                data:{code:res.code},
                                header: {
                                    'content-type': 'application/json', // 默认值
                                    'api-token': token,
                                    'api-name' : 'wxapp'
                                },
                                success(res){
                                    if(res.data.code == 1){
                                        let token = res.data.data.token.token;
                                        wx.setStorageSync("token",token);
                                        request(url,data,resolve)
                                    }
                                },
                                fail(err){
                                    console.log(err)
                                }
                            })
                        }
                    })
                }else{
                    res.data.info == '登录认证不能为空！' ? '' : (wx.showToast({
                        title: res.data.info,
                        icon: 'none',
                    }))
                    reject(res.data.info)
                }
            },
            fail(){
                wx.showToast({
                title: '接口请求错误',
                icon:'none'
                })
                reject('接口请求错误')
            },
            complete(){
                wx.hideLoading({})
            }
        })
    })
}
module.exports = {
    request
}