import { Call_controller } from "corejs/form_crud";
import input from "corejs/inputErrManager";
class Contact {
  constructor(element) {
    this.element = element;
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find(".page-content");
    this.contact_frm = this.wrapper.find("#contact-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    /**
     * Submit contact form
     * ==================================================
     */

    phpPlugin.wrapper.on("submit", "#contact-frm", function (e) {
      e.preventDefault();
      const data = {
        url: "contact_form",
        frm: $(this),
        frm_name: $(this).attr("id"),
        table: "contacts",
      };
      Call_controller(data, (response) => {
        if (response.result == "success") {
          phpPlugin.wrapper.find("#alertErr").html(response.msg);
        } else {
          if (response.result == "error-field") {
            input.error(phpPlugin.contact_frm, response.msg);
          } else {
            phpPlugin.wrapper.find("#alertErr").html(response.msg);
          }
        }
      });
    });
    /**
     * Reset input on focus
     * ==========================================================
     */
    input.removeInvalidInput(phpPlugin.contact_frm);
  };
}
document.addEventListener("DOMContentLoaded", function () {
  new Contact($("#main-site"))._init();
});
