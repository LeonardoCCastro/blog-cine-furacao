import { computed } from 'vue'

  // linksRef deve ser uma ref / computed que retorne o array atual (ex: toRef(props, 'links'))
export function usePagination(linksRef) {
  const hasLinks = computed(() => Array.isArray(linksRef.value) && linksRef.value.length > 0)

  const activeLink = computed(() => hasLinks.value ? linksRef.value.find(l => l.active) : null)
  
  const activeNum = computed(() => {
    if (!activeLink.value) return 1
    const n = parseInt(String(activeLink.value.label).replace(/\D/g, ''), 10)
    return isNaN(n) ? 1 : n
  })

  const lastLink = computed(() => {
    if (!hasLinks.value) return null
    return linksRef.value.length >= 2 ? linksRef.value[linksRef.value.length - 2] : null
  })

  const lastNum = computed(() => {
    if (!lastLink.value) return 1
    const n = parseInt(String(lastLink.value.label).replace(/\D/g, ''), 10)
    return isNaN(n) ? 1 : n
  })

  const compactLinks = computed(() => {
    if (!hasLinks.value) return []

    const pages = []
    const first = linksRef.value[0]
    const second = linksRef.value[1]
    const last = linksRef.value[linksRef.value.length - 1]

    if (first) pages.push(first)
    if (second) pages.push(second)

    const active = activeNum.value
    const lastP = lastNum.value

    if (active > 1) {
      const prevPage = linksRef.value.find(l => {
        const n = parseInt(String(l.label).replace(/\D/g, ''), 10)
        return !isNaN(n) && n === active - 1
      })
      if (prevPage && parseInt(String(prevPage.label).replace(/\D/g, ''), 10) !== 1) {
        pages.push(prevPage)
      }
    }

    if (active !== 1 && active !== lastP) {
      if (activeLink.value) pages.push(activeLink.value)
    }

    if (active < lastP) {
      const nextPage = linksRef.value.find(l => {
        const n = parseInt(String(l.label).replace(/\D/g, ''), 10)
        return !isNaN(n) && n === active + 1
      })
      if (nextPage && parseInt(String(nextPage.label).replace(/\D/g, ''), 10) !== lastP) {
        pages.push(nextPage)
      }
    }

    if (lastLink.value) {
      const lastNumber = parseInt(String(lastLink.value.label).replace(/\D/g, ''), 10)
      if (!isNaN(lastNumber) && lastNumber !== 1) {
        const already = pages.some(p => String(p.label).replace(/\D/g, '') === String(lastNumber))
        if (!already) pages.push(lastLink.value)
      }
    }

    if (last) pages.push(last)

    return pages.filter(Boolean)
  })

  const shouldShow = computed(() => {
    if (!hasLinks.value) return false
    const numeric = linksRef.value.filter(l => !isNaN(parseInt(String(l.label).replace(/\D/g, ''), 10)))
    return numeric.length > 1
  })

  return {
    hasLinks,
    activeLink,
    activeNum,
    lastLink,
    lastNum,
    compactLinks,
    shouldShow
  }
}
