/**
  *@Version : 1.0
  *@Author : Wayde Sun
  *@Time : 2016.5.4
  */

  ih.defineClass('ih.plugins.rootViewController', null, null, function(ROOT, root){
  
    root.prototype.init = function(){
      this.dm = new ih.plugins.rootDataModel();
      this.dm.delegate = this;
      this.setupErrorinfo();
      this.setupClickEvent();

      if(this.dm.sysUser.isLogin()) {
        this.setUserinfo();
      }
    };
    
    root.prototype.setupClickEvent = function(){
      var me = this;
      $('#accountpassword').bind('keypress',function(event){
        if(event.keyCode == "13") {
          me.onSignInBtnClicked();
        }
      });

      $("#ih-register-btn").click(ih.$F(function(){
        this.onRegisterBtnClicked();
      }).bind(this));
      $("#ih-login-btn").click(ih.$F(function(){
        this.onSignInBtnClicked();
      }).bind(this));
      $("#ih-forgetPwd-btn").click(ih.$F(function(){
        this.onForgetPwdMaskBtnClicked();
      }).bind(this));

      $("#mask-button").click(ih.$F(function(){
        this.onCloseMaskBtnClicked();
      }).bind(this));
    };
    
    root.prototype.onRegisterBtnClicked = function(){
      $("#ds_container").html(this.registerHtml);
      
      $("#register-cancel").click(ih.$F(function(){
        this.onCloseMaskBtnClicked();
      }).bind(this));
      $("#register-sure").click(ih.$F(function(){
        this.onRegisterSureBtnClicked();
      }).bind(this));
    };
    
    root.prototype.onRegisterSureBtnClicked = function(){
      var accountName = $("#accountname")[0].value;
      var accountPassword = $("#accountpassword")[0].value;
      var confirmPassword = $("#confirmpassword")[0].value;
      
      if(!accountName || !accountPassword || !confirmPassword){
        this.showMessage({title:"温馨提示", text:"三项均不能为空"});
        return;
      } else if(accountPassword != confirmPassword) {
        this.showMessage({title:"温馨提示", text:"密码确认不相等，请重新输入"});
        $("#accountpassword").val("");
        $("#confirmpassword").val("");
        return;
      }
      
      var target = document.getElementById('ds_container');
      this.registerSpinner = new Spinner(ih.plugins.rootPlugin.spinnerDefaultOpts).spin(target);
      this.dm.doRegister({ihakulaID:accountName, password:accountPassword, confirmPwd:confirmPassword});
    };
    
    root.prototype.registerSuccess = function(){
      this.registerSpinner.stop();
      $("#ds_container").html(this.loginHtml);
      this.showMessage({title:"温馨提示", text:this.languages[this.selectedLanguage]["registerSucceed"]});
      
    };
    
    root.prototype.registerFailed = function(errorCode){
      this.registerSpinner.stop();
      this.showMessage({title:"温馨提示", text:this.errorInfo[errorCode]});
    };
    
    root.prototype.onSignInBtnClicked = function(){
      var accountName = $("#accountname")[0].value;
      var accountPassword = $("#accountpassword")[0].value;
      
      if(!accountName || !accountPassword){
        this.showMessage({title:"温馨提示", text:"请输入用户名和密码"});
        return;
      }
      
      var target = document.getElementById('ds_container');
      this.registerSpinner = new Spinner(ih.plugins.rootPlugin.spinnerDefaultOpts).spin(target);
      this.dm.doLogin({ihakulaID:accountName, password:accountPassword});
    };
    
    root.prototype.loginSuccess = function(){
      this.registerSpinner.stop();
      this.onCloseMaskBtnClicked();
      this.setUserinfo();
    };
    
    root.prototype.loginFailed = function(errorCode){
      this.registerSpinner.stop();
      this.onCloseMaskBtnClicked();
      this.showMessage({title:"温馨提示", text:this.errorInfo[errorCode]});
    };
    
    root.prototype.setUserinfo = function(){
      $("#ih-hi").html("hi," + this.dm.sysUser.name);
      $("#ih-login-button").html("Logout");
      $("#ih-login-button").unbind("click");
      $("#ih-login-button").click(ih.$F(function(){
      var me = this;
        $('#dialog').dialog({
            autoOpen: false,
            width: 600,
            title: "温馨提示",
            buttons: {
                "Ok": function() {
                  me.setUserLogout();
                  $(this).dialog("close");
                },
                "Cancel": function() {
                    $(this).dialog("close");
                }
            }
        });

        // Dialog Link
        $('#dialog').html("确认登出？").dialog('open');
      }).bind(this));
    };
    
    root.prototype.setUserLogout = function(){
      this.dm.sysUser.logout();
      $("#ih-hi").html("");
      $("#ih-login-button").html("Login");
      $("#ih-login-button").unbind("click");
      $("#ih-login-button").click(ih.$F(function(){
        this.onLoginBtnClicked();
      }).bind(this));
    };
    
    root.prototype.onForgetPwdMaskBtnClicked = function(){
      this.showMessage({title:"温馨提示", text:"Coming soon"});
    };
    
    root.prototype.onCloseMaskBtnClicked = function(){
      $("#ds_container").addAnimation("bounceOutUp");
      $("#ih-mask").addAnimation("fadeOut");
      var tempF = function(){
        $("#ih-mask").css("display", "none");
      };
      window.setTimeout(tempF, 2000);
    };
    
    root.prototype.showMessage = function(dialogMsg){
      $('.error').css("display", "none");
      $('')

      // Dialog
        $('#dialog').dialog({
            autoOpen: false,
            width: 600,
            title: dialogMsg.title,
            buttons: {
                "Sure": function() {
                    $(this).dialog("close");
                }
            }
        });

        // Dialog Link
        $('#dialog').html(dialogMsg.text).dialog('open');
    };
    
    root.prototype.setupErrorinfo = function(){
      this.errorInfo = {
        900 : "hello error"
      };
    };

  });

  window.ihSysEngine.pubsub.publish("ihRootPluginReady");
