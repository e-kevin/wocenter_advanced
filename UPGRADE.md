# 升级帮助文件

## v0.3
该版本新增了一个`Command`应用，日后有关WoCenter的控制台命令均存放于此。

以下为全新安装的操作步骤：

1. 新建数据库`wocenter_advanced`
2. 执行以下命令：
```bash
$ git clone https://github.com/<username>/wocenter_advanced.git #重新克隆项目
$ cd wocenter_advanced #进入项目根目录
$ composer install -v #安装依赖
$ ./cmd installation #安装wocenter项目
```