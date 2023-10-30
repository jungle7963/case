const {
  request
} = require('./request')
const { baseUrl } = require('./env').dev
module.exports = {
    //登录
    login: (data) => request('data/api.wxapp/session', data),

    //图片上传
    upload: `${baseUrl}data/api.auth.Center/upload`,

    //首页
    banner: (data) => request('data/api.Tasks/getSlider', data),
    taskType: (data) => request('data/api.Tasks/getTaskCate', data),
    taskList: (data) => request('data/api.Tasks/getTasks', data),
    getTasks: (data) => request('data/api.auth.Tasks/getTasks', data),
    getBanner: (data) => request('data/api.auth.Tasks/getBanner', data),
    getTaskDetails: (data) => request('data/api.auth.Tasks/getTaskDetails', data),


    //我的
    saveUser: (data) => request('data/api.auth.Center/set', data),
    me: (data) => request('data/api.auth.me/get', data),
    withDrawal: (data) => request('data/api.auth.me/withDrawal', data),
    withDrawalList: (data) => request('data/api.auth.me/withDrawalList', data),
    rank: (data) => request('data/api.auth.me/rank', data),
    opinion: (data) => request('data/api.auth.me/opinion', data),

    //任务
    taskDetails: (data) => request('data/api.Tasks/getTaskDetails', data),
    addTask: (data) => request('data/api.auth.Tasks/addTask', data),
    taskStatusList: (data) => request('data/api.auth.Tasks/taskStatusList', data),
    taskCancel: (data) => request('data/api.auth.Tasks/taskCancel', data),
    taskProcess: (data) => request('data/api.auth.Tasks/taskProcess', data),
    taskImage: (data) => request('data/api.auth.Tasks/getTaskImage', data),
}