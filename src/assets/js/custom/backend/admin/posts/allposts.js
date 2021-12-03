import "plugins/flexadmin/js/ecommerce";
import Cruds from "corejs/crud_interface";
import media from "corejs/upload_interface";
import select2 from "corejs/select2_manager";
import editorLoader from "corejs/editorLoad";

class AllPosts {
  constructor(element) {
    this.element = element;
    this.selectTag = [];
  }
  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };
  _setupVariables = () => {
    this.wrapper = this.element.find("#allposts-wrapper");
    this.modalbox = this.element.find("#modal-box");
    this.modalform = this.element.find("#modal-box #posts-frm");
    this.editorContent = ["postContent"];
  };
  _setupEvents = () => {
    var phpPlugin = this;
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    /**
     * Editor Loader
     * ==========================================================================
     */
    const loader = new editorLoader(phpPlugin.editorContent, {
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_posts_page",
    });
    /**
     * Open modal
     * =====================================================================
     */
    const __open_modal = async () => {
      const bs = await import(
        /* webpackChunkName: "bsmodal" */ "corejs/bootstrap_modal"
      );
      new bs.default(["modal-box"])._init().then((modal) => {
        modal[0].show();
      });
    };
    /**
     * Init Crud Operations
     * ==========================================================================
     */

    let cruds = new Cruds({
      table: "posts",
      wrapper: phpPlugin.wrapper,
      form: phpPlugin.modalform,
      modal: phpPlugin.modalbox,
      bsmodal: "modal-box",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "all_posts_page",
      ck_content: phpPlugin.editorContent,
      loader: loader,
    });

    /**
     * Display All Items
     * ==========================================================================
     */

    cruds._displayAll({
      datatable: true,
      data_type: "values",
      model_method: "getAll",
    });

    /**
     * Add btn
     * =======================================================================
     */
    cruds._set_addBtn();
    /**
     * Add or Update post
     * ======================================================================
     */
    phpPlugin.modalbox.on("submit", "#posts-frm", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#submitBtn").val("Please wait...");
      cruds._add_update({
        datatable: true,
        swal: true,
        modal: true,
        categorie: phpPlugin.modalbox.find(".categorie"),
        media: phpPlugin.modalbox.find("#p_media"),
        select: phpPlugin.selectTag,
        data_type: "values",
        // model_method: "get_Products",
        frm: $(this),
        frm_name: $(this).attr("id"),
        imageUrlsAry: Array.from(
          new DOMParser()
            .parseFromString(loader.editor.postContent.data.get(), "text/html")
            .querySelectorAll("img")
        ).map((img) => img.getAttribute("src")),
        folder: "posts",
      });
    });
    /**
     * Edit form
     * =======================================================================
     */
    phpPlugin.wrapper.on("click", ".editBtn", function (e) {
      e.preventDefault();
      phpPlugin.modalform.find("#operation").val("update");
      const data = {
        std_fields: [
          "postID",
          "postAuthor",
          "postCommentCount",
          "postContent",
          "postDate",
          "postImg",
          "postTitle",
          "updateAt",
          "userID",
          "postStatus",
          "deleted",
        ],
        tbl_options: [],
        table: "posts",
        frm: $(this).parents("form").length != 0 ? $(this).parents("form") : "",
        frm_name: $(this).parents("form").attr("id"),
        tag: $(this),
      };
      if (!loader.check()) {
        loader.load().then(() => {
          __open_modal();
          cruds._edit(data);
        });
      } else {
        __open_modal();
        cruds._edit(data);
      }
    });

    /**
     * Clean Form and server
     * =====================================================================
     */
    cruds._clean_form({
      select: phpPlugin.selectTag,
      cke: true,
    });
    //Clean temporary data
    document
      .querySelector('[data-bs-dismiss="modal"]')
      .addEventListener("click", (e) => {
        e.preventDefault();
        console.log("dismiss");
        const data = {
          table: "post_file_url",
          folder: "posts",
          csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
          frm_name: "all_posts_page",
          url: "cleanTempFiles",
        };
        cruds._cleanTempFiles(data);
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
    });
    cruds._active_inactive_elmt({ table: "categories" });
  };
}
// Dropzone.autoDiscover = false;
document.addEventListener("DOMContentLoaded", () => {
  new AllPosts($(".page-container"))._init();
});
