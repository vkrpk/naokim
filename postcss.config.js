module.exports = {
  plugins: {
    // include whatever plugins you want
    // but make sure you install these via yarn or npm!

    // add browserslist config to package.json (see below)
    autoprefixer: {},
  },
};

const path = require("path");

Encore.enablePostCssLoader((options) => {
  options.postcssOptions = {
    // the directory where the postcss.config.js file is stored
    config: path.resolve(__dirname, "sub-dir", "custom.config.js"),
  };
});
