import request from '@/utils/request'

export function getCategory(query) {
  return request({
    url: '/admin/youtube/categories',
    method: 'post',
    data: query
  })
}

export function getList(query) {
  return request({
    url: '/admin/information/index',
    method: 'post',
    data: query
  })
}

export function getListcate(query) {
  return request({
    url: '/admin/information/index',
    method: 'post',
    data: query
  })
}

export function getListAll() {
  return request({
    url: '/admin/information/getlists',
    method: 'post'
  })
}

export function getinfo(id) {
  return request({
    url: '/admin/information/getinfo',
    method: 'post',
    data: { id }
  })
}

export function save(data) {
  return request({
    url: '/admin/information/save',
    method: 'post',
    data
  })
}

export function del(id) {
  return request({
    url: '/admin/information/del',
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
    url: '/admin/information/change',
    method: 'post',
    data
  })
}

export function delAll(data) {
  return request({
    url: '/admin/information/delall',
    method: 'post',
    data
  })
}

export function changeAll(data) {
  return request({
    url: '/admin/information/changeall',
    method: 'post',
    data
  })
}
