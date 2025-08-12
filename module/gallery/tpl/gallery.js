(() => {
  'use strict'
  window.addEventListener('load', () => {
    let html = []
        const tag_img = '<div class="carousel-item" item-key="%s"><h6></h6><img src="%s" class="d-block w-100" alt="" loading="lazy"></div>'
    document.querySelector('#galleryList .list-group').querySelectorAll('img')
      ?.forEach((el,i) => {
            html[i] = tag_img.sprintf(el.src.getQuery('file'),'./common/img/blank.png')
      })
    let inner = document.querySelector('#galleryContentModal .carousel-inner')
    inner.innerHTML = html.join('')

    const getImage = (id, srl) => {
      let item = inner.querySelector('.carousel-item[item-key="'+srl+'"]')
      exec_ajax({
        module:'gallery',
        act:'getFiles',
        md_id:id,
        mf_srls:srl
      })
      .then((data)=>{
          data.forEach((el) => {
            let img = item.querySelector('img')
            img.src = './?file='+el.mf_srl
            img.setAttribute('alt', el.mf_name)
            item.querySelector('h6').innerHTML = (el.mf_name || '')+', '+(el.mf_regdate || '')
            return
          })
      })
      .catch((error)=>{
        console.log(error)
        item.querySelector('h6').innerHTML = error
      })
    }

    const md_id = current_url.getQuery('id')

    document.querySelector('#galleryContentModal')
      ?.addEventListener('show.bs.modal', e => {
        let srl = e.relatedTarget.querySelector('img')?.src.getQuery('file')
        inner.querySelectorAll('.carousel-item.active')?.forEach((el) => {el.classList.remove('active')})
        inner.querySelector('.carousel-item[item-key="'+srl+'"]').classList.add('active')
        getImage(md_id, srl);
      })

      document.getElementById('carouselGallery')
        ?.addEventListener('slide.bs.carousel', function (e) {
          if((e.from == 0 && e.direction == 'right') || (e.to == 0 && e.direction == 'left')){
            e.preventDefault()
            e.stopPropagation()
            let items = document.getElementById('paginationGallery').querySelectorAll('li.page-number')
            confirm($_LANG['confirm_page_' + e.direction])
              .then(()=>{
                items.forEach((el,i) => {
                  if(el.hasAttribute('selected')){
                    items[e.direction == 'left' ? i+1 : i-1]?.querySelector('a').click()
                    return
                  }
                })
              })
              .catch(error => console.log(error))
          } else {
            getImage(md_id, e.relatedTarget.getAttribute('item-key'));
          }
        })
    })
})()