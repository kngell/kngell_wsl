import OP from "corejs/operator";
import { Call_controller } from "corejs/form_crud";
import input from "corejs/inputErrManager";
import StripeAPI from "corejs/stripeAPIClient";
import { Modal } from "bootstrap";
import select2 from "corejs/select2_manager";
import log_reg from "corejs/logregloader";
import credit_card from "img/chip.png";

class Checkout {
  constructor(element) {
    this.element = element;
  }

  _init = () => {
    this._setupVariables();
    this._setupEvents();
  };

  _setupVariables = () => {
    this.progressbar = this.element.find(".progressbar");
    this.prevBtns = document.querySelectorAll(".btn-prev");
    this.nextBtns = document.querySelectorAll(".btn-next");
    this.progressLine = document.querySelector("#progress");
    this.formSteps = document.querySelectorAll(".form-step");
    this.progressSteps = document.querySelectorAll(".progress-step");

    this.navigation = this.element.find(".navigation");
    this.tab_content = this.element.find(".tab-content");
    this.wrapper = this.element.find(".page-content");
    this.modal_email = this.element.find("#modal-box-email");
    this.modal_address_change = this.element.find("#modal-box-change-address");
    this.modal_address_add = this.element.find("#modal-box-add-address");
    this.modal_shipping = this.element.find("#modal-box-shipping");
    this.pay_box = document.getElementById("payment-box");

    this.bsmodal_address_change = Modal.getOrCreateInstance(
      document.getElementById("modal-box-change-address")
    );
    this.bsmodal_address_add = Modal.getOrCreateInstance(
      document.getElementById("modal-box-add-address")
    );
    this.bsmodal_shipping = Modal.getOrCreateInstance(
      document.getElementById("modal-box-shipping")
    );
    this.address_type = "shipping";
  };

  _setupEvents = () => {
    var phpPlugin = this;

    /**
     * Select 2
     * ========================================================================
     */
    const select = new select2();
    const csrftoken = document.querySelector('meta[name="csrftoken"]');
    select._init({
      element: phpPlugin.wrapper.find("#pays"),
      placeholder: "Please select a country",
      url: "get_countries",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "checkout_page",
    });

    select._init({
      element: phpPlugin.modal_shipping.find("#shipping_class_change"),
      placeholder: "Please select a shipping class",
      url: "showDetails",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "checkout_page",
      dropdownParent: phpPlugin.modal_shipping,
      tbl_options: "shipping_class",
    });

    select._init({
      element: phpPlugin.modal_address_add.find("#chg-pays"),
      placeholder: "Please select a country",
      url: "get_countries",
      csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
      frm_name: "checkout_page",
      dropdownParent: phpPlugin.modal_address_add,
    });

    /**
     * Init stripe JS
     * ========================================================================
     */
    const stripeApi = new StripeAPI({
      api_key: phpPlugin.wrapper.find("#stripe_key").val(), // ok
      cardHolderLname: document.getElementById("chk-lastName"), //ok
      cardHolderFname: document.getElementById("chk-firstName"), //ok
      cardElement: document.getElementById("card-element"),
      cardExp: document.getElementById("card-exp"), //ok
      cardCvc: document.getElementById("card-cvc"), //ok
      cardError: document.getElementById("card-error"), //ok
      cardErrorID: "#card-error",
      cardButton: document.getElementById("complete-order"), //ok
      cardButtonID: "#complete-order",
      responseError: document.getElementById("stripeErr"),
    });
    /**
     * Clean forms
     * ========================================================================
     */
    input.removeInvalidInput(phpPlugin.modal_email.find("#change-email-frm"));
    input.removeInvalidInput(phpPlugin.modal_email.find("#change-address-frm"));
    input.removeInvalidInput(phpPlugin.wrapper);
    document
      .getElementById("modal-box-email")
      .addEventListener("hide.bs.modal", function () {
        const frm = phpPlugin.modal_email.find("#change-email-frm");
        frm.get(0).reset();
        if (frm.find(".is-invalid").length != 0) {
          input.reset_invalid_input(frm);
        }
      });

    /**
     * Create stripe credit card
     * ========================================================================
     */
    stripeApi._create_cardElements();
    /**
     * Format Money
     * =======================================================================
     */
    const operation = new OP();
    operation._format_money({
      wrapper: phpPlugin.wrapper,
      fields: [
        ".sub-total .amount",
        ".p-price",
        ".res-tax-item .amount",
        ".total-ttc .amount",
        ".total-ht",
        ".price",
      ],
    });
    phpPlugin.wrapper.on(
      "click",
      ".payment-gateway input[name='pm_name']",
      function (e) {
        phpPlugin.wrapper
          .find("#cc_firstName")
          .val(phpPlugin.wrapper.find("#chk-firstName").val());
        phpPlugin.wrapper
          .find("#cc_lastName")
          .val(phpPlugin.wrapper.find("#chk-lastName").val());
      }
    );

    /**
     * Payment form show
     * =======================================================================
     */
    phpPlugin.wrapper.on("click", "#pay-now", () => {
      var data = {
        url: "check_user_cart",
        csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
        frm_name: "checkout_page",
      };
      Call_controller(data, (response) => {
        if (response.result == "success") {
          const p_b = Modal.getOrCreateInstance(
            document.getElementById("payment-box")
          );
          p_b.show();
        } else {
          phpPlugin.wrapper.find("#alertErr").html(response.msg);
        }
      });
    });

    phpPlugin.pay_box.addEventListener("show.bs.modal", function () {
      $(this)
        .find(".card_holder")
        .val(function () {
          return (
            phpPlugin.wrapper.find("#chk-firstName").val() +
            " " +
            phpPlugin.wrapper.find("#chk-lastName").val()
          );
        });
    });
    /**
     * Submit Checkout form
     * =======================================================================
     */
    phpPlugin.wrapper.on("submit", "#user-ckeckout-frm", function (e) {
      e.preventDefault();
      phpPlugin.wrapper.find("#complete-order").text("Please wait...");
      const frm = $(this);
      const pm_name = phpPlugin.wrapper
        .find(".payment-gateway input[name='pm_name']")
        .filter(":checked")
        .val();
      var data = {
        url: "check_paymentMode",
        csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
        frm_name: "checkout_page",
        pm_name: pm_name,
        params: { frm: frm, pm_name: pm_name },
      };
      Call_controller(data, (response, params) => {
        if (response.result == "success") {
          Object.getOwnPropertyNames(data).forEach(function (prop) {
            delete data[prop];
          });
          data.url = "placeOrder";
          data.frm = params.frm;
          data.frm_name = params.frm.attr("id");
          switch (true) {
            case params.pm_name == 1:
              stripeApi
                ._createPayment()
                .then((paymentMethod) => {
                  data.paymentMethod = paymentMethod;
                  Call_controller(data, (response) => {
                    if (response.result == "success") {
                      window.location.href = response.msg;
                    } else {
                      if (response.result == "error-field") {
                        input.error(phpPlugin.wrapper, response.msg1);
                        phpPlugin.wrapper.find("#alertErr").html(response.msg2);
                      } else {
                        phpPlugin.wrapper
                          .find("#pmt_ErrMsg")
                          .html(response.msg);
                      }
                    }
                    const placeOrder = "<span> Complete order</span>";
                    phpPlugin.wrapper.find("#complete-order").html(placeOrder);
                  });
                })
                .catch((error) => {
                  console.log(error);
                  var data = {
                    url: "manage_paymentModeSelectErrors",
                    csrftoken: document
                      .querySelector('meta[name="csrftoken"]')
                      .getAttribute("content"),
                    frm_name: "checkout_page",
                    error: error,
                  };
                  Call_controller(data, (response) => {
                    phpPlugin.wrapper.find("#alertErr").html(response.msg);
                  });
                  const placeOrder = "<span> Complete order</span>";
                  phpPlugin.wrapper.find("#complete-order").html(placeOrder);
                });
              break;
            case params.pm_name == 2:
              console.log("paypal");
              break;

            default:
              console.log("default");
              phpPlugin.wrapper
                .find("#alertErr")
                .html(
                  "<h6 class='text-center'>This Payment method is not set yet! Please choose another one</h6>"
                );
              break;
          }
        } else {
          phpPlugin.wrapper.find("#alertErr").html(response.msg);
          const placeOrder =
            '<span> Complete order</span> <i class="fal fa-angle-double-right"></i> <span id="button-text">Pay now</span>';
          phpPlugin.wrapper.find("#complete-order").html(placeOrder);
        }
      });
    });

    /**
     * Change Email
     * =======================================================================
     */
    phpPlugin.modal_email.on("submit", "#change-email-frm", function (e) {
      e.preventDefault();
      var data = {
        url: "manage_changeEmail",
        frm: $(this),
        frm_name: $(this).attr("id"),
      };
      Call_controller(data, (response) => {
        if (response.result == "success") {
          phpPlugin.wrapper.find(".contact-email").text(response.msg);
          const bsmodal_email = Modal.getOrCreateInstance(
            document.getElementById("modal-box-email")
          );
          bsmodal_email.hide();
        } else {
          if (response.result == "error-field") {
            input.error(phpPlugin.modal_email, response.msg);
          } else {
            phpPlugin.wrapper.find("#alertErr").html(response.msg);
          }
        }
      });
    });

    /**
     * Update Shipping Infos
     * =======================================================================
     */
    function update_shippingInfos(response) {
      phpPlugin.wrapper
        .find(".total-shipping .title")
        .html(response.msg.shipping.name);
      phpPlugin.wrapper
        .find(".total-shipping .amount")
        .html(response.msg.shipping.price);
      phpPlugin.wrapper.find(".total-ttc .amount").html(response.msg.ttc);
      phpPlugin.wrapper
        .find(".shipping_method .method_title")
        .html(response.msg.shipping.name);
      phpPlugin.wrapper
        .find(".shipping_method .price")
        .html(response.msg.shipping.price);
      operation._format_money({
        wrapper: phpPlugin.wrapper,
        fields: [
          ".total-shipping .amount",
          ".total-ttc .amount",
          ".shipping_method .price",
        ],
      });
    }
    /**
     * Update Shipping method
     * =======================================================================
     */
    phpPlugin.wrapper.on(
      "change",
      "#shipping-information .radio__input",
      function () {
        const data = {
          url: "manage_changeShipping",
          csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
          frm_name: "checkout_page",
          shipping_class_change: $(this).val(),
        };
        Call_controller(data, (response) => {
          update_shippingInfos(response);
        });
      }
    );
    /**
     * Change Shipping mode
     * =======================================================================
     */
    phpPlugin.modal_shipping.on("submit", "#shipping-select-frm", function (e) {
      e.preventDefault();
      var data = {
        url: "manage_changeShipping",
        frm: $(this),
        frm_name: $(this).attr("id"),
      };
      Call_controller(data, (response) => {
        if (response.result == "success") {
          update_shippingInfos(response);
          phpPlugin.bsmodal_shipping.hide();
        }
      });
    });

    phpPlugin.wrapper.on(
      "change",
      "#order-billing-address .radio-check__wrapper input",
      function (e) {
        e.preventDefault();

        if ($(this).val() === "2") {
          phpPlugin.bsmodal_address_change.show();
          phpPlugin.address_type = "billing";
        } else {
          var data = {
            url: "manage_changeAdress",
            csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
            frm_name: "checkout_page",
          };
          Call_controller(data, (response) => {
            console.log(response);
            phpPlugin.wrapper.find(".bill-to-address").text(response.msg);
          });
        }
      }
    );
    /**
    php
     * Change Address Naviation
     * =======================================================================
     */
    phpPlugin.modal_address_change.on("click", ".card", function (e) {
      $(this)
        .addClass("card--active")
        .parent()
        .siblings()
        .children()
        .removeClass("card--active");
    });
    phpPlugin.wrapper.on("click", ".change-bill__btn", function () {
      phpPlugin.address_type = "billing";
    });
    phpPlugin.wrapper.on("click", ".change-ship__btn", function () {
      phpPlugin.address_type = "shipping";
    });
    /**
     * Closing Change Adress Modal and update shippind address
     * ======================================================================
     */
    phpPlugin.modal_address_change.on("click", ".closeAddress", function (e) {
      e.preventDefault();
      var data = {
        url: "manage_changeAdress",
        csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
        frm_name: "checkout_page",
        id: $(this)
          .parents(".address-book-wrapper")
          .find(".card--active")
          .children()
          .first()
          .val(),
        address_type: phpPlugin.address_type,
      };
      Call_controller(data, (response) => {
        if (phpPlugin.address_type == "shipping") {
          phpPlugin.wrapper.find(".ship-to-address").text(response.msg);
        } else {
          phpPlugin.wrapper.find(".bill-to-address").text(response.msg);
          phpPlugin.address_type = "shipping";
          if (
            phpPlugin.wrapper
              .find(
                "#order-billing-address .radio-check__wrapper input[type='radio']"
              )
              .filter(":checked")
              .val() === "2"
          ) {
            if (
              phpPlugin.wrapper
                .find("#billing-information .bill-address-ckeck")
                .is(":hidden")
            ) {
              phpPlugin.wrapper
                .find("#billing-information .bill-address-ckeck")
                .show();
            }
          }
        }

        phpPlugin.bsmodal_address_change.hide();
      });
    });

    /**
     * Show Modal to add new address
     * ======================================================================
     */
    phpPlugin.modal_address_change.on("click", ".addAddress", function (e) {
      e.preventDefault();
      phpPlugin.bsmodal_address_add.show();
    });

    /**
     * Add new Address to user address book list
     * ======================================================================
     */
    phpPlugin.modal_address_add.on("submit", "#add-address-frm", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var data = {
        url: "manage_AddAdress",
        frm: $(this),
        frm_name: $(this).attr("id"),
      };
      Call_controller(data, (response) => {
        if (response.result == "success") {
          phpPlugin.modal_address_change
            .find("#showAdress")
            .append(response.msg);
          phpPlugin.bsmodal_address_add.hide();
        } else {
          if (response.result == "error-field") {
            input.error(phpPlugin.modal_address, response.msg);
          } else {
            phpPlugin.wrapper.find("#alertErr").html(response.msg);
          }
        }
      });
    });
    /**
     * Get Checkout data From User Session
     * ======================================================================
     */
    function get_checkoutSession() {
      var data = {
        url: "get_checkoutSession",
        csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
        frm_name: "checkout_page",
      };
      Call_controller(data, (response) => {
        phpPlugin.wrapper.find(".contact-email").html(response.msg.email);
        phpPlugin.wrapper
          .find(".ship-to-address")
          .html(response.msg.ship_address.name);
        phpPlugin.wrapper
          .find(".bill-to-address")
          .html(response.msg.bill_address.name);
        phpPlugin.wrapper
          .find(".shipping_method .method_title")
          .html(response.msg.shipping.name);
        phpPlugin.wrapper
          .find(".shipping_method .price")
          .html(response.msg.shipping.price);
        operation._format_money({
          wrapper: phpPlugin.wrapper,
          fields: [".shipping_method .price"],
        });
      });
    }

    let formStepNum = 0;
    phpPlugin.nextBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const data = {
          url: "proceedToBuy",
          csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
          frm_name: "checkout_page",
        };
        Call_controller(data, (response) => {
          if (response.result == "success") {
            if (response.msg == "login-required") {
              (async () => {
                const bs = await import(
                  /* webpackChunkName: "bsmodal" */ "corejs/bootstrap_modal"
                );
                console.log(bs);
                bs.login_modal.toggle();
                var loader = new log_reg().check();
                if (!loader.isLoad) {
                  loader.login();
                  phpPlugin.wrapper
                    .find("#input_checkout")
                    .val("chk_navigation");
                }
              })();
            } else {
              formStepNum++;
              updateFromStep();
              updateProgressBar();
              get_checkoutSession();
            }
          }
        });
      });
    });
    phpPlugin.prevBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const data = {
          url: "proceedToBuy",
          csrftoken: csrftoken ? csrftoken.getAttribute("content") : "",
          frm_name: "checkout_page",
        };
        Call_controller(data, (response) => {
          if (response.result == "success") {
            if (response.msg == "login-required") {
              (async () => {
                const bs = await import(
                  /* webpackChunkName: "bsmodal" */ "corejs/bootstrap_modal"
                );
                bs.login_modal.toggle();
                var loader = new log_reg().check();
                if (!loader.isLoad) {
                  loader.login();
                }
                phpPlugin.wrapper.find("#input_checkout").val("chk_navigation");
              })();
            } else {
              formStepNum--;
              updateFromStep();
              updateProgressBar();
              get_checkoutSession();
            }
          }
        });
      });
    });
    function updateFromStep() {
      phpPlugin.formSteps.forEach((formStep) => {
        formStep.classList.contains("form-step-active") &&
          formStep.classList.remove("form-step-active");
      });
      phpPlugin.formSteps[formStepNum].classList.add("form-step-active");
    }

    function updateProgressBar() {
      phpPlugin.progressSteps.forEach((progressStep, idx) => {
        if (idx < formStepNum + 1) {
          progressStep.classList.add("progress-step-active");
        } else {
          progressStep.classList.remove("progress-step-active");
        }
      });
      const progressStepActive = document.querySelectorAll(
        ".progress-step-active"
      );
      phpPlugin.progressLine.style.width =
        ((progressStepActive.length - 1) /
          (phpPlugin.progressSteps.length - 1)) *
          100 +
        "%";
    }
  };
}
document.addEventListener("DOMContentLoaded", function () {
  new Checkout($("#body"))._init();
});
