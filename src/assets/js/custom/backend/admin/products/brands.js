import Cruds from "corejs/crud_interface";
class Brand {
  constructor(element) {
    this.element = element;
  }
  /**
   * Init
   */
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  /**
   * Setp variables
   */
  _setupVariables = () => {
    this.wrapper = this.element.find("#brand-wrapper");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.modalbox.find("#brands-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "brand",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_brand_page",
      select_tag: phpPlugin.selectTag,
    });

    /**
     * Display All Items
     * ==========================================================================
     */
    cruds._displayAll({
      datatable: true,
      data_type: "values",
    });

    /**
     * Add or Update
     * =======================================================================
     */
    phpPlugin.modalbox.on("submit", "#brands-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        data_type: "values",
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
          "brID",
          "br_name",
          "br_descr",
          "status",
          "updated_at",
          "created_at",
          "deleted",
        ],
        table: "brand",
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
      inputHidden: ["operation", "brID", "created_at", "updated_at", "deleted"],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-brand-frm",
      data_type: "values",
      frm: true,
    });

    /**
     * Manage Status
     * =================================================================
     */
    cruds._active_inactive_elmt({ table: "brand" });
  };
}
// Dropzone.autoDiscover = false;
document.addEventListener("DOMContentLoaded", () => {
  new Brand($(".page-content"))._init();
});
