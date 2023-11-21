import request from '@/utils/request'

export function query(query) {
  return request({
    url: '/admin/youtube/query',
    method: 'post',
    data: query
  })
}

export function caption(query) {
  return request({
    url: '/admin/youtube/caption',
    method: 'post',
    data: query
  })
}

export function captions(link) {
  return request({
    url: '/admin/youtube/captions',
    method: 'post',
    data: {link}
  })
}

export function serach(query) {
  return request({
    url: '/admin/youtube/serach',
    method: 'post',
    data: query
  })
}

export function saveQuery(query) {
  return request({
    url: '/admin/youtube/saveQuery',
    method: 'post',
    data: query
  })
}

export function saveQueryAll(query) {
  return request({
    url: '/admin/youtube/saveQueryAll',
    method: 'post',
    data: query
  })
}

export function getprogress() {
  return request({
    url: '/admin/youtube/getprogress',
    method: 'post',
  })
}

export function saveScreenshot(data) {
  return request({
    url: '/admin/youtube/saveScreenshot',
    method: 'post',
    data
  })
}

export function saveLink(data) {
  return request({
    url: '/admin/youtube/saveLink',
    method: 'post',
    data
  })
}

export function download(query) {
  return request({
    url: '/admin/youtube/download',
    method: 'post',
    data: query,
  })
}

export function mp4Download(query) {
  return request({
    url: '/admin/youtube/mp4Download',
    method: 'post',
    data: query,
  })
}

export function mp3Transcription(query) {
  return request({
    url: '/admin/youtube/mp3Transcription',
    method: 'post',
    data: query,
  })
}

export function ossUpload(query) {
  return request({
    url: '/admin/youtube/ossUpload',
    method: 'post',
    data: query,
  })
}

export function mp3Translate(query) {
  return request({
    url: '/admin/youtube/mp3Translate',
    method: 'post',
    data: query,
  })
}

export function getinfo(id) {
  return request({
    url: '/admin/youtube/getinfo',
    method: 'post',
    data: { id }
  })
}

export function save(data) {
  return request({
    url: '/admin/youtube/save',
    method: 'post',
    data
  })
}

export function publish(data) {
  return request({
    url: '/admin/youtube/publish',
    method: 'post',
    data
  })
}

export function release(data) {
  return request({
    url: '/admin/youtube/release',
    method: 'post',
    data
  })
}

export function getList(query) {
  return request({
    url: '/admin/youtube/index',
    method: 'post',
    data: query
  })
}

export function getListAll() {
  return request({
    url: '/admin/youtube/getlists',
    method: 'post'
  })
}

export function ytdel(id) {
  return request({
    url: '/admin/youtube/ytdel',
    method: 'post',
    data: { id }
  })
}

export function del(id) {
  return request({
    url: '/admin/youtube/del',
    method: 'post',
    data: { id }
  })
}

export function change(id, field, value) {
  const data = {
    val: id,
    field: field,
    value: value
  }
  return request({
    url: '/admin/youtube/change',
    method: 'post',
    data
  })
}

export function delAll(data) {
  return request({
    url: '/admin/youtube/delall',
    method: 'post',
    data
  })
}

export function changeAll(data) {
  return request({
    url: '/admin/youtube/changeall',
    method: 'post',
    data
  })
}
