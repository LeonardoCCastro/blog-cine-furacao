<script setup>
import { computed } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    required: true
  }
})

// Pega a página ativa
const activeLink = props.links.find(l => l.active)
const activeNum = activeLink ? parseInt(activeLink.label) : 1

// Pega a última página
const lastLink = props.links[props.links.length - 2] // penúltimo é a última numérica
const lastNum = lastLink ? parseInt(lastLink.label) : 1

// Cria array compacto
const compactLinks = computed(() => {
  const pages = []

  // Previous
  pages.push(props.links[0])

  // Página 1
  pages.push(props.links[1])

  // Página anterior
  if (activeNum > 1 && activeNum - 1 !== 1) pages.push(props.links.find(l => parseInt(l.label) === activeNum - 1))

  // Página atual
  if (activeNum !== 1 && activeNum !== lastNum) pages.push(activeLink)

  // Próxima página
  if (activeNum < lastNum && activeNum + 1 !== lastNum) pages.push(props.links.find(l => parseInt(l.label) === activeNum + 1))

  // Última página
  if (lastNum !== 1) pages.push(lastLink)

  // Next
  pages.push(props.links[props.links.length - 1])

  return pages.filter(Boolean) // remove nulls
})

</script>

<template>
  <nav class="mt-8 sm:mt-12">
    <ul class="flex items-center justify-center -space-x-px">
      <li v-for="(link, index) in compactLinks" :key="index">
        <Link v-if="link.url" :href="link.url" preserve-scroll class="flex items-center justify-center px-3 h-8 leading-tight border" :class="[index === 0 ? 'rounded-s-lg border-e-0' : '', index === compactLinks.length - 1 ? 'rounded-e-lg' : '', link.active ? 'text-blue-600 border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white']" v-html="link.label" />
        <span v-else :class="[index === 0 ? 'rounded-s-lg border-e-0' : '', index === compactLinks.length - 1 ? 'rounded-e-lg' : '' ]" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-gray-100 border border-gray-300 cursor-not-allowed dark:bg-gray-700 dark:border-gray-700 dark:text-gray-500" v-html="link.label" />
      </li>
    </ul>
  </nav>
</template>