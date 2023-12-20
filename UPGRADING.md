# Upgrading

## Upgrading from 1.x to 2.x

The only breaking change is that the `Vue` attribute has been split into `Vue`, `VueProp`, and `VueRef`. The `Vue` attribute can still be used for methods. For passing PHP properties to Vue, you may now use either `VueProp` or `VueRef`. `VueProp` is a one-way binding, while `VueRef` is a two-way binding. Previously, two-way binding was the only option.