import request from '@/utils/request'

export function getList(query) {
  return request({
    url: '/admin/wordpress/index',
    method: 'post',
    data: query
  })
}

export function release(data) {
  return request({
    url: '/admin/wordpress/release',
    method: 'post',
    data
  })
}

export function getListcate(query) {
  return request({
    url: '/admin/wordpress/index',
    method: 'post',
    data: query
  })
}

export function getListAll() {
  return request({
    url: '/admin/wordpress/getlists',
    method: 'post'
  })
}

export function getinfo(id) {
  return request({
    url: '/admin/wordpress/getinfo',
    method: 'post',
    data: { id }
  })
}

export function save(data) {
  return request({
    url: '/admin/wordpress/save',
    method: 'post',
    data
  })
}

export function del(id) {
  return request({
    url: '/admin/wordpress/del',
    method: 'post',
    data: { id }
  })
}

export function delwp(id) {
  return request({
    url: '/admin/wordpress/delwp',
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
    url: '/admin/wordpress/change',
    method: 'post',
    data
  })
}

export function delAll(data) {
  return request({
    url: '/admin/wordpress/delall',
    method: 'post',
    data
  })
}

export function changeAll(data) {
  return request({
    url: '/admin/wordpress/changeall',
    method: 'post',
    data
  })
}
