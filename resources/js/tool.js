Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-config',
      path: '/nova-config',
      component: require('./components/Tool'),
    },
  ])
})
