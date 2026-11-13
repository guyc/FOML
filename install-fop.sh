#!/bin/sh
# Install the pinned Apache FOP release into this directory and point the
# `fop` symlink at it.  Foml::$fopExec runs fop/fop, so switching versions is:
# bump VERSION and SHA512 below, rerun this script.  Safe to rerun.
#
# SHA512 comes from https://archive.apache.org/dist/xmlgraphics/fop/binaries/fop-$VERSION-bin.tar.gz.sha512
set -eu

VERSION=2.12
SHA512=975c54953cf409b41b61fb46e6bc6c629e2a62f1d9c79c219e28fadaccb4428968fe44344cd5b5bd64b418ebd9d4522a66d98c5033d92572c7349a68fcc32e20
URL=https://archive.apache.org/dist/xmlgraphics/fop/binaries/fop-$VERSION-bin.tar.gz

cd "$(dirname "$0")"

if [ ! -x "fop-$VERSION/fop/fop" ]; then
    tmp=$(mktemp)
    trap 'rm -f "$tmp"' EXIT
    echo "Downloading FOP $VERSION"
    curl -sSfL -o "$tmp" "$URL"
    echo "$SHA512  $tmp" | sha512sum -c --quiet -
    tar xzf "$tmp"
fi

ln -sfn "fop-$VERSION/fop" fop
mkdir -p .fop   # font cache; must be writable by the web server user
echo "fop -> fop-$VERSION/fop"
fop/fop -version
