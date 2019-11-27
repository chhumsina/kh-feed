import Vue from 'vue'

export function truncate(text, length, clamp) {
  clamp = clamp || '...';
  var node = document.createElement('div');
  node.innerHTML = text;
  var content = node.textContent;
  return content.length > length ? content.slice(0, length) + clamp : content;
}

export function getImgUrl(image, type, size) {
  return `${process.env.baseUrl}image/${type}/${size}/${image}`;
}

const filters = {truncate, getImgUrl}

export default Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key])
})
