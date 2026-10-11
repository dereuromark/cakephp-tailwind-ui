import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'CakePHP TailwindUi',
  description: 'CakePHP helpers for Tailwind CSS, DaisyUI, and KTUI.',
  base: '/cakephp-tailwind-ui/',
  cleanUrls: true,
  srcExclude: ['README.md'],
  head: [['link', { rel: 'icon', href: '/cakephp-tailwind-ui/favicon.svg', type: 'image/svg+xml' }]],
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Installation', link: '/installation' },
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'Helpers', link: '/helpers' },
      { text: 'Plugins', link: 'https://dereuromark.github.io/' },
    ],
    sidebar: [{ text: 'TailwindUi plugin', items: [
      { text: 'Installation', link: '/installation' },
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'Compile your CSS', link: '/guide/css' },
      { text: 'Helpers', link: '/helpers' },
      { text: 'Class map', link: '/class-map' },
      { text: 'Presets', link: '/presets' },
      { text: 'Bake templates', link: '/bake' },
      { text: 'Screenshots', link: '/screenshots' },
      { text: 'Upgrading', link: '/guide/upgrading' },
    ] }],
    search: { provider: 'local' },
    socialLinks: [{ icon: 'github', link: 'https://github.com/dereuromark/cakephp-tailwind-ui' }],
    editLink: { pattern: 'https://github.com/dereuromark/cakephp-tailwind-ui/edit/master/docs/:path' },
    footer: { message: 'Released under the MIT License.', copyright: 'Copyright Mark Scherer' },
  },
})
