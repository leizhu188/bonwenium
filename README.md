# bonwenium
php自动化测试框架

##  目录
#### app/actions
常规步骤组件：<br>
例如针对个人主页测试，个人收藏测试等，都需要登录后展开，故登录等 常规操作可写于acitons。
#### app/scenarios
情景步骤组件：<br>
例如针对登录进行场景测试，如手机号格式，密码格式等多样化场景测试时，写于scenarios。
#### app/functions
自定义步骤组件：<br>
例如输入验证码等特殊处理操作，需要扩展代码分析，故提供functions，继承主Controller，可以处理程序同时操作当前的webdriver实例。
#### app/steps
生产线：<br>
也称为组件集/步骤集，所有组件取自以上三种（常规，情景，自定义）组件，可以根据测试用例任意搭配。<br>
一条完整的生产线可以成为一个完整需求的全套自动化测试。
#### config/app.php
应用常规配置：<br>
如 测试网站入口，日志导出方式，点击停顿时间等等。
#### config/web_driver.php
webdriver驱动配置：<br>
驱动url，监听端口，驱动浏览器等。

##  组件开发者文档
组件以字符串数组的方式实现element操作组合,一个字符串实现一个操作。<br>
#### 事件操作：<br>
子集定位，以>>标识子集查找，查找类型 id,class,tag<br>
`例   >>tag:li`<br>
同级筛选，以>标识同级筛选，查找类型 tag,id,text,num,css等<br>
`例   >text:登录`<br>
`例 查找第0个   >num:0`<br>
具体操作，以>>>标识具体操作， write,append,click等<br>
`例  >>>write:新概念`<br>
`例 点击   >>>click`<br>
按上述可实现任何鼠标点击或者键盘写入操作，<br>
`例 tag:div>>tag:button>text:登录>>>click`<br>
#### 常规组件/情景组件：<br>
以事件操作数组的形式将不同事件按场景分析组合而成，遵循事件定位原则，有如下模式：<br><br>
must_step：必须执行的事件。<br>
`例 "must_step" => "tag:button>text:登录>>>click"`<br><br>
should_step：非必须执行的事件。<br>
`例 "should_step" => "tag:button>text:登录>>>click"`<br><br>
sleep：停顿（秒）。<br>
`例 "sleep" => 2`<br><br>
usleep：停顿（微秒）。<br>
`例 "usleep" => 500000`<br><br>
scroll：滚动到指定位置<br>
`例 "scroll" => "0,0"`<br><br>
until：当某element出现/消失后。<br>
`例 "until" => "tag:input>text:验证码>>>appear"`<br>
`例 "until" => "tag:button>text:重试>>>disappear"`<br><br>
asserts：断言。<br>
`例 "asserts"=>["tag:button>text:发送>>>exist","tag:span>text:密码错误>>>no_exist"]`<br><br>
function：自定义组件。<br>
`例 "function"=>"User@writeResetPwdCaptcha@{"phone":18810680772}"`<br>
`调用functions下的User.php中writeResetPwdCaptcha函数，参数以json定义，框架会自动解析并存入类变量datas中。可以使用$this->datas['phone']调用`<br><br>
所以bonwenium极大幅度简化了开发者的代码量，只需掌握html和js基础知识，就可以编写出一个理想的自动化测试项目。

#### 生产线：<br>
steps:由常规组件、情景组件、自定义组件等自由组合而成。一条生产线完成一个功能甚至一个完整项目的自动化测试。




