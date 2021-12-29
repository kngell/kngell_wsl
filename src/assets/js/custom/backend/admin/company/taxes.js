import Cruds from "corejs/crud_interface";
import select2 from "corejs/select2_manager";
class Taxes {
  constructor(element) {
    this.element = element;
    this.selectTag = ["categorieID"];
  }

  /**
   * Init
   * =======================================================================
   */
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };

  /**
   * Setup variables
   * =======================================================================
   */
  _setupVariables = () => {
    this.wrapper = this.element.find(".card");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#taxes-frm");
  };

  /**
   * Setup events
   * =======================================================================
   *
   */
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');

    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "taxes",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      select_tag: phpPlugin.selectTag,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_taxes_page",
    });

    /**
     * display All items
     * =======================================================================
     */
    cruds._displayAll({
      datatable: true,
      data_type: "spcefics_values",
    });

    /**
     * Select2 ajax
     * =======================================================================
     *
     */
    const select = new select2();
    select._init({
      element: phpPlugin.modalform.find("#categorieID"),
      tbl_options: "categories",
      placeholder: "Please select a associate Categorie",
      url: "showDetails",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });
    /**
     * Add or Update Ware House
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#taxes-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        select: phpPlugin.selectTag,
        data_type: "spcefics_values",
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
          "tID",
          "t_name",
          "t_descr",
          "t_rate",
          "t_class",
          "status",
          "created_at",
          "updated_at",
          "deleted",
          "categorieID",
        ],
        tbl_options: ["categories"],
        table: "taxes",
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
      inputHidden: ["operation", "tID", "created_at", "updated_at", "deleted"],
    });
    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-taxe-frm",
      data_type: "values",
      frm: true,
    });

    //Activate item
    cruds._active_inactive_elmt({ table: "taxes" });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new Taxes($(".page-content"))._init();
});
