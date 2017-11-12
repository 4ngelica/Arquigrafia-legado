# Javascript/Vue.js Modules

This is the Javascript/Vue.js modules inside Arquigrafia.

## Installation

- You must have npm or yarn. And before working, run:

```bash
yarn install
```

- This libraries are using webpack, to bundle the multiples Javascript file into a one bundled file. To start webpack to reload changes:

```bash
yarn start
```

- Remember to keep packages in package.json updated. This will prevent deprecation changes.

- After all changes, remember to create your final javascript modules build:

```bash
yarn build
```

## Folder Organization

- javascript/components
  - Store all the Vue.js components.

- javascript/containers
  - A container is the interface between the PHP pages and the Javascript components.
  - All the folders inside "containers" must have a index.js, that will be bundled by Webpack.
  - The index.js will pass the data got by PHP and pass to components.
  - If the page use Vue.js components, here we put the Vue.js container, this container is responsible to mount the page the way we desire.

- javascript/services
  - A service is a set of functions.
  - Here, we do all the calculation things, all the logic.
  - Network goes here too.

- javascript/\_\_tests\_\_
  - Store the jest tests.