#!/usr/bin/perl
use strict;
use warnings;
use CGI qw(:standard);
use Cwd 'abs_path';
use File::Basename;

# ===== KONFIG =====
my $BASE_DIR = "/data/www";   # GANTI ke root folder yg boleh dibuka
# ==================

my $q = CGI->new;
my $path = $q->param("path") || $BASE_DIR;

$path = abs_path($path);

# Proteksi biar gak keluar BASE_DIR
if (!$path || index($path, $BASE_DIR) != 0) {
    print header(-status => 403);
    print "Access denied";
    exit;
}

print header(-type => "text/html; charset=utf-8");

print <<HTML;
<!DOCTYPE html>
<html>
<head>
<title>Perl CGI File Manager</title>
<style>
body { font-family: Arial; background:#111; color:#eee }
a { color:#4fc3f7; text-decoration:none }
table { width:100%; border-collapse: collapse }
th, td { padding:8px; border-bottom:1px solid #333 }
tr:hover { background:#222 }
</style>
</head>
<body>

<h2>📁 File Manager (Perl CGI)</h2>
<p>Path: <b>$path</b></p>
<table>
<tr><th>Nama</th><th>Tipe</th><th>Ukuran</th></tr>
HTML

# Tombol UP
if ($path ne $BASE_DIR) {
    my $up = dirname($path);
    print "<tr><td><a href='?path=$up'>⬆️ ..</a></td><td>DIR</td><td>-</td></tr>";
}

opendir(my $dh, $path) or die "Cannot open directory";
while (my $f = readdir($dh)) {
    next if $f =~ /^\./;
    my $full = "$path/$f";

    if (-d $full) {
        print "<tr><td>📁 <a href='?path=$full'>$f</a></td><td>DIR</td><td>-</td></tr>";
    } else {
        my $size = -s $full;
        print "<tr><td>📄 <a href='?download=$full'>$f</a></td><td>FILE</td><td>$size</td></tr>";
    }
}
closedir $dh;

print "</table></body></html>";

# ===== DOWNLOAD =====
if (my $dl = $q->param("download")) {
    $dl = abs_path($dl);
    exit unless index($dl, $BASE_DIR) == 0;

    open my $fh, "<", $dl or exit;
    binmode $fh;

    print header(
        -type => 'application/octet-stream',
        -attachment => basename($dl)
    );
    print while <$fh>;
    close $fh;
    exit;
}