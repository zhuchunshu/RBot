Go_Bin="/usr/local/go/bin/go"
if [[ ! -x "$Go_Bin" ]]; then
     echo "开始下载go资源包 \n";

wget https://golang.google.cn/dl/go1.17.6.linux-amd64.tar.gz -O go.tar.gz
echo "下载完毕，开始安装\n\n";
tar -C /usr/local -xzf go.tar.gz
echo "安装完成!\n\n";
rm -rf "`pwd`/go.tar.gz"
cd "`pwd`/../app/RBot/Core"
/usr/local/go/bin/go version
/usr/local/go/bin/go env -w GOPROXY=https://goproxy.cn,direct && /usr/local/go/bin/go build -ldflags "-s -w -extldflags '-static'" -o BotServer
echo "编译完成"
exit
else
cd "`pwd`/../app/RBot/Core"
/usr/local/go/bin/go version
/usr/local/go/bin/go env -w GOPROXY=https://goproxy.cn,direct && /usr/local/go/bin/go build -ldflags "-s -w -extldflags '-static'" -o BotServer
echo "编译完成"
exit

fi