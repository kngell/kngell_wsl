import Cruds from "corejs/crud_interface";
import select2 from "corejs/select2_manager";
class AllCategories {
  constructor(element) {
    this.element = element;
    this.selectTag = ["parentID", "brID"];
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
    this.modalform = this.element.find("#categorie-frm");
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
      table: "categories",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_categories_page",
      select_tag: phpPlugin.selectTag,
    });

    /**
     * Display All Items
     * ==========================================================================
     */
    cruds._displayAll({
      datatable: true,
      data_type: "values",
      model_method: "getAllCategories",
    });
    /**
     * Select 2 Categories initialization
     * ====================================================================
     */
    const select = new select2()._init({
      url: "showDetails",
      element: phpPlugin.modalform.find("#parentID"),
      tbl_options: "categories",
      placeholder: "Select a parent Categorie",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    select._init({
      element: phpPlugin.modalform.find("#brID"),
      tbl_options: "brand",
      placeholder: "Select a brand",
      url: "showDetails",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    /**
     * Add or Update
     * =======================================================================
     */
    phpPlugin.modalbox.on("submit", "#categorie-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        data_type: "values",
        frm: $(this),
        frm_name: $(this).attr("id"),
        model_method: "getAllCategories",
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
          "catID",
          "date_enreg",
          "updateAt",
          "status",
          "categorie",
          "description",
          "parentID",
          "brID",
        ],
        tbl_options: ["categories", "brand"],
        table: "categories",
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
      inputHidden: ["catID", "date_enreg", "updateAt", "deleted", "operation"],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-categorie-frm",
      data_type: "values",
      frm: true,
    });

    /**
     * Manage Status
     * ====================================================================
     */
    cruds._active_inactive_elmt({ table: "categories" });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new AllCategories($(".page-container"))._init();
});
