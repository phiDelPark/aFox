

(() => {
  'use strict'
  window.addEventListener('load', () => {
    document.querySelector('#galleryContentModal')
      ?.addEventListener('show.bs.modal', e => {
        const body = e.target.querySelector('.carousel-inner'),
          querys = e.relatedTarget.href.getQuery(),
          group = e.relatedTarget.closest('.list-group'),
          active = e.relatedTarget.querySelector('img')?.src.getQuery('file'),
          imgs = group.querySelectorAll('img')
        let srls = ''
        imgs.forEach(el => {srls += el.src.getQuery('file') + ','})
        exec_ajax({
          module:'gallery',
          act:'getFiles',
          md_id:querys['id'],
          mf_srls:srls
        })
        .then((data)=>{
          let html =''
          const tag_img = '<div class="carousel-item%s">%s<img src="%s" class="d-block w-100" alt="%s" loading="lazy"></div>'
          data.forEach(el => {
            html += tag_img.sprintf(
              active==el.mf_srl?' active':'',
              '<h6>'+(el.mf_name || '')+', '+(el.mf_regdate || '')+'</h6>',
              './?file='+el.mf_srl, el.mf_name
            )
          })
          body.innerHTML = html
        })
        .catch(error => console.log(error))
      })
  })
})()