import "plugins/flexadmin/js/ecommerce";
import Cruds from "corejs/crud_interface";
import media from "corejs/upload_interface";
import select2 from "corejs/select2_manager";

class AllProducts {
  constructor(element) {
    this.element = element;
    this.selectTag = [
      "p_company",
      "p_warehouse",
      "p_shipping_class",
      "p_unitID",
    ];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find("#allproducts-wrapper");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#new-product-frm");
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');

    /**
     * Init Crud Operations
     * ==========================================================================
     */
    let cruds = new Cruds({
      table: "products",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_product_page",
    });

    /**
     * Display All Items
     * ==========================================================================
     */

    cruds._displayAll({
      datatable: true,
      data_type: "values",
      model_method: "get_Products",
    });

    /**
     * Select 2 initialization
     * ====================================================================
     */
    const myselect2 = new select2();
    myselect2._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".p_shipping_class"),
      tbl_options: "shipping_class",
      placeholder: "Select a shipping class",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
      multiple: true,
    });

    const unit = myselect2._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".p_unitID"),
      tbl_options: "units",
      placeholder: "Select a unit",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });

    const company = myselect2._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".p_company"),
      tbl_options: "company",
      placeholder: "Select a Company",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
    });
    const warehouse = new select2()._init({
      url: "showDetails",
      element: phpPlugin.modalform.find(".p_warehouse"),
      placeholder: "Select a wareHouse",
      csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
      frm_name: phpPlugin.modalform.attr("id"),
      tbl_options: "warehouse",
    });
    company.select.on("change", function () {
      warehouse._destroy();
      if ($(this).select2("data") != 0) {
        warehouse.params.parentID = Object.values(
          $(this).select2("data")
        )[0].id;
        console.log(warehouse);
      }
      warehouse._init({
        element: warehouse.select,
        url: "showDetails",
        tbl_options: "warehouse",
        parentElt: "company",
        placeholder: "Select a wareHouse",
        csrftoken: phpPlugin.modalform.find("input[name='csrftoken']").val(),
        frm_name: phpPlugin.modalform.attr("id"),
      });
    });
    /**
     * Upload Media
     * ======================================================================
     */
    let dropzone = new media({
      dz_element: phpPlugin.modalform.find(".dragAndDrop__wrapper"),
      submitBtn: "submitBtn1",
    })._upload();
    /**
     * Add or Update product
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#new-product-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        categorie: phpPlugin.modalbox.find(".categorie"),
        media: phpPlugin.modalbox.find("#p_media"),
        dropzone: dropzone,
        select: phpPlugin.selectTag,
        data_type: "values",
        frm: $(this),
        frm_name: $(this).attr("id"),
        folder: "products",
        model_method: "get_Products",
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
          "pdtID",
          "p_unitID",
          "p_title",
          "p_short_descr",
          "p_descr",
          "p_regular_price",
          "p_compare_price",
          "p_cost_per_item",
          "p_charge_tax",
          "p_media",
          "p_sku",
          "p_barre_code",
          "p_track_qty",
          "p_continious_sell",
          "p_qty",
          "p_back_border",
          "p_stock_threshold",
          "p_weight",
          "p_lenght",
          "p_width",
          "p_height",
          "p_shipping_class",
          "p_warehouse",
          "p_company",
          "created_at",
          "updated_at",
          "deleted",
        ],
        inputElement: phpPlugin.modalbox.find("#myfile"),
        categorieElement: phpPlugin.modalform.find("#check-box-wrapper"),
        table: "products",
        tbl_options: [
          "categories",
          "company",
          "warehouse",
          "shipping_class",
          "units",
        ],
        frm: $(this).parents("form").length != 0 ? $(this).parents("form") : "",
        frm_name: $(this).parents("form").attr("id"),
        tag: $(this),
        dropzone: dropzone,
      });
    });

    /**
     * Clean Form and server
     * =====================================================================
     */
    cruds._clean_form({
      select: phpPlugin.selectTag,
      dropzone: dropzone,
      inputHidden: [
        "pdtID",
        "created_at",
        "updated_at",
        "operation",
        "deleted",
      ],
    });

    /**
     * Delete
     * =====================================================================
     */
    cruds._delete({
      swal: true,
      datatable: true,
      url_check: "checkdelete",
      delete_frm_class: ".delete-product-frm",
      data_type: "values",
      frm: true,
      folder: "products",
    });

    /**
     * Product Status
     * =======================================================================
     */
    cruds._active_inactive_elmt({ table: "categories" });
  };
}
// Dropzone.autoDiscover = false;
document.addEventListener("DOMContentLoaded", () => {
  new AllProducts($(".page-container"))._init();
});
