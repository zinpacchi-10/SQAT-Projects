/**
 * main.js
 * Small vanilla-JS helper for making XMLHttpRequest-based AJAX calls to
 * the app's api/* JSON endpoints (used by every role's AJAX feature).
 */
function ajaxPost(url, data, onSuccess, onError) {
  const xhr = new XMLHttpRequest();
  xhr.open('POST', url, true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      try {
        const res = JSON.parse(xhr.responseText);
        if (xhr.status >= 200 && xhr.status < 300) {
          onSuccess(res);
        } else {
          (onError || defaultAjaxError)(res);
        }
      } catch (e) {
        (onError || defaultAjaxError)({ message: 'Unexpected server response.' });
      }
    }
  };
  const params = new URLSearchParams(data).toString();
  xhr.send(params);
}

function ajaxGet(url, onSuccess, onError) {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', url, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      try {
        const res = JSON.parse(xhr.responseText);
        if (xhr.status >= 200 && xhr.status < 300) {
          onSuccess(res);
        } else {
          (onError || defaultAjaxError)(res);
        }
      } catch (e) {
        (onError || defaultAjaxError)({ message: 'Unexpected server response.' });
      }
    }
  };
  xhr.send();
}

function defaultAjaxError(res) {
  alert(res && res.message ? res.message : 'Something went wrong. Please try again.');
}
