import Cruds from "corejs/crud_interface";
import select2 from "corejs/select2_manager";
class Shipping {
  constructor(element) {
    this.element = element;
    this.selectTag = ["sh_name"];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find(".card");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#shipping-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');

    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "shipping_class",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      select_tag: phpPlugin.selectTag,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_shippingClass_page",
    });

    /**
     * Display all items
     * =======================================================================
     *
     */
    cruds._displayAll({
      datatable: true,
      data_type: "values",
    });

    /**
     * Select2 Tag init
     * =======================================================================
     */
    const select = new select2();
    select._init({
      element: phpPlugin.modalform.find("#sh_name"),
      tbl_options: "shipping_class",
      placeholder: "Please select a shipping class",
      url: "showDetails",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    /**
     * Add or Update Ware House
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#shipping-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        select: phpPlugin.selectTag,
        data_type: "values",
        // model_method: "get_Products",
        frm: $(this),
        frm_name: $(this).attr("id"),
      });
    });
    phpPlugin.wrapper.find("#addNew").on("click", function () {
      phpPlugin.modalform.find("#operation").val("add");
    });

    /**
     * Edit form
     * =======================================================================
     */
    phpPlugin.wrapper.on("click", ".editBtn", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#operation").val("update");
      cruds._edit({
        std_fields: [
          "shcID",
          "sh_name",
          "sh_descr",
          "status",
          "price",
          "created_at",
          "updated_at",
        ],
        tbl_options: ["shipping_class"],
        table: "shipping_class",
        frm: $(this).parents("form").length != 0 ? $(this).parents("form") : "",
        frm_name: $(this).parents("form").attr("id"),
        tag: $(this),
      });
    });
    /**
     * Clean Form and server
     * =====================================================================
     */
    cruds._clean_form({
      select: phpPlugin.selectTag,
      inputHidden: ["operation", "shcID", "created_at", "updated_at"],
    });
    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-shipping-class",
      data_type: "values",
      frm: true,
    });
    //Activate item
    cruds._active_inactive_elmt({ table: "shipping_class" });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new Shipping($(".page-container"))._init();
});
