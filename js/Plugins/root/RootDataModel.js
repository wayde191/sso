
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
    };
    
    dm.prototype.doLogin = function(paras){
        paras.sCode = this.sCode;
      this.request.callService(paras, ih.$F(function(response){
        if (1 == response.status) {
            this.sysUser = response.user;
            this.delegate.loginSuccess();
            this.pubsub.publish("loginSucceed");
        } else {
            this.delegate.loginFailed(response.errorCode);
        }
      }).bind(this), ih.rootUrl + "IhUser/login", "POST");
    };
    
    dm.prototype.doRegister = function(paras){
        paras.sCode = this.sCode;
      this.request.callService(paras, ih.$F(function(response){
              console.log(response);
              if (1 == response.status) {
                  this.delegate.registerSuccess();
              } else {
                  this.delegate.registerFailed(response.errorCode);
              }
          }).bind(this), ih.rootUrl + "IhUser/register", "POST");
    };

      dm.prototype.checkMobile = function(phoneNo){
          return /^1[3|4|5|8][0-9]\d{4,8}$/.test(phoneNo) ? true : false;
      }

  });