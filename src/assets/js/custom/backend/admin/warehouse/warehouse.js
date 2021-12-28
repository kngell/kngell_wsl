import Cruds from "corejs/crud_interface";
import select2 from "corejs/select2_manager";
class Warehouse {
  constructor(element) {
    this.element = element;
    this.selectTag = ["company", "country_code"];
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
    this.modalform = this.modalbox.find("#warehouse-frm");
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
      table: "warehouse",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_warehouse_page",
      select_tag: phpPlugin.selectTag,
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
      element: phpPlugin.modalform.find("#company"),
      tbl_options: "company",
      placeholder: "Please select a company",
      url: "showDetails",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    select._init({
      element: phpPlugin.modalform.find("#country_code"),
      placeholder: "Please select a Country",
      url: "get_countries",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });
    /**
     * Add or Update Ware House
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#warehouse-frm", function (e) {
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
          "whID",
          "wh_name",
          "wh_descr",
          "status",
          "created_at",
          "company",
          "country_code",
          "updated_at",
          "deleted",
        ],
        tbl_options: ["company", "countries"],
        table: "warehouse",
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
      inputHidden: ["operation", "whID", "created_at", "updated_at", "deleted"],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-warehouse-frm",
      data_type: "values",
      frm: true,
    });

    //Activate item
    cruds._active_inactive_elmt({ table: "warehouse" });
  };
}
document.addEventListener("DOMContentLoaded", () => {
  new Warehouse($("#main-site"))._init();
});
