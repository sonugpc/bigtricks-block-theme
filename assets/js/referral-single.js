(function () {
  "use strict";

  function showTab(tabId, button) {
    var tab = document.getElementById(tabId);
    if (!tab) return;

    document.querySelectorAll(".tab-content").forEach(function (node) {
      node.classList.remove("active");
    });

    document.querySelectorAll(".tab-btn").forEach(function (btn) {
      btn.classList.remove("active");
      btn.classList.remove(
        "text-slate-700",
        "dark:text-slate-300",
        "border-primary-500",
        "dark:border-primary-400",
      );
      btn.classList.add(
        "text-slate-500",
        "dark:text-slate-500",
        "border-transparent",
      );
    });

    tab.classList.add("active");

    if (button) {
      button.classList.add("active");
      button.classList.remove(
        "text-slate-500",
        "dark:text-slate-500",
        "border-transparent",
      );
      button.classList.add(
        "text-slate-700",
        "dark:text-slate-300",
        "border-primary-500",
        "dark:border-primary-400",
      );
    }
  }

  // Required because template buttons call showTab(...) inline.
  window.showTab = showTab;

  document.addEventListener("DOMContentLoaded", function () {
    if (
      window.bigtricksReferralSingle &&
      window.bigtricksReferralSingle.isSubmitted
    ) {
      var target =
        document.getElementById("user-codes") ||
        document.getElementById("referral-submit");
      if (target) {
        var y = target.getBoundingClientRect().top + window.scrollY - 100;
        window.scrollTo({ top: y, behavior: "smooth" });
      }
    }
  });
})();
