"use strict";

const _page_options = [
  {
    template: "./src/views/client/errors/_errors.php",
    inject: "body",
    filename: "[name].[ext]",
  },
];

class PagesConfig {
  static get page_options() {
    return _page_options;
  }
}

module.exports = PagesConfig;
