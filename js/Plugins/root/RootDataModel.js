
/*
 * Copyright (c) 2012-2020, iHakula Studio Software Inc.
 * Use, modification, and distribution subject to terms of license.
 * @version: 1.0
 * @date: 3/29/2013
 * @author: Wayde Sun
 * @email: hakula@ihakula.com
 * @website: www.ihakula.com
 */

  ih.defineClass("ih.plugins.rootDataModel", null, null, function(DM, dm){
  
    dm.prototype.init = function(){
        this.request = new ih.Service();
        this.sysUser = {};
        this.pubsub = new ih.PubSub();
        this.delegate = null;
        this.sCode = 'iHakulaSecurityCode2016';

        this.setWhiteList();
        this.setRedirectUrl();
    };
    
    dm.prototype.doLogin = function(paras){
      this.request.callService(this.wrapParameters(paras), ih.$F(function(response){
        if (1 == response.status) {
            this.sysUser = response.user;
            this.delegate.loginSuccess();
            this.pubsub.publish("loginSucceed");
        } else {
            this.delegate.loginFailed(response.errorCode);
        }
      }).bind(this), ih.rootUrl + "ihuser/login", "POST");
    };
    
    dm.prototype.doRegister = function(paras){
      this.request.callService(this.wrapParameters(paras), ih.$F(function(response){
              if (1 == response.status) {
                  this.delegate.registerSuccess();
              } else {
                  this.delegate.registerFailed(response.errorCode);
              }
          }).bind(this), ih.rootUrl + "ihuser/register", "POST");
    };

      dm.prototype.checkMobile = function(phoneNo){
          return /^1[3|4|5|8][0-9]\d{4,8}$/.test(phoneNo) ? true : false;
      };

      dm.prototype.wrapParameters = function (paras) {
          paras.sCode = this.sCode;
          return paras;
      };

      dm.prototype.getParameter = function(name) {
          var url = document.location.href;
          var start = url.indexOf("?")+1;
          if (start==0) {
              return "";
          }
          var value = "";
          var queryString = url.substring(start);
          var paraNames = queryString.split("&");
          for (var i=0; i<paraNames.length; i++) {
              if (name == this.getParameterName(paraNames[i])) {
                  value = this.getParameterValue(paraNames[i])
              }
          }
          return value;
      };

      dm.prototype.getParameterName = function(str) {
          var start = str.indexOf("=");
          if (start==-1) {
              return str;
          }
          return str.substring(0,start);
      };

      dm.prototype.getParameterValue = function(str) {
          var start = str.indexOf("=");
          if (start==-1) {
              return "";
          }
          return str.substring(start+1);
      };

      dm.prototype.setRedirectUrl = function(){
          var url = this.getParameter('redirect');
          this.redirectUrl = url;
          if( this.redirectUrl === '' ){
              gotoIHakula();
          } else {
              if(!contains(this.whiteList, this.redirectUrl)){
                  gotoIHakula();
              }
          }
      };

      dm.prototype.setWhiteList = function(){
          this.whiteList = ['http://localhost:3000/productions.html'];
      };

      dm.prototype.redirectCallback = function(){
          window.location.href = this.redirectUrl + '?' +
          'token=' + this.sysUser.token +
          '&id=' + this.sysUser.id;
      };

      function gotoIHakula(){
          window.location.href = 'http://www.ihakula.com/';
      };

      function contains(a, obj) {
          for (var i = 0; i < a.length; i++) {
              if (a[i] === obj) {
                  return true;
              }
          }
          return false;
      }

  });