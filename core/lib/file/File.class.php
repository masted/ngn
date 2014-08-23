<?php

class File {

  static function strings($file) {
    return array_map('trim', explode("\n", trim(str_replace("\r", '', file_get_contents($file)))));
  }

  static function delete($file) {
    if (file_exists($file)) unlink($file);
  }

  static function name($file) {
    return preg_replace('/^(.+)\.[^.]+$/', '$1', basename($file));
  }

  static function getUnicName($folder, $ext) {
    $name = time().'.'.$ext;
    while (file_exists($folder.'/'.$name)) {
      $name = time().rand(0, 1000).'.'.$ext;
    }
    return $name;
  }

  static function reext($filepath, $ext) {
    return self::stripExt($filepath).'.'.$ext;
  }

  static function stripExt($filepath) {
    return preg_replace('/^(.*)\.\w+$/', '$1', $filepath);
  }

  static function getExtension($file) {
    return self::getExtensionByMime(self::getMime($file));
  }

  static function getMime($file) {
    File::checkExists($file);
    $r = getOS() == 'win' ? finfo_open(FILEINFO_MIME) : //finfo_open(FILEINFO_MIME, '/usr/share/file/magic');
      finfo_open(FILEINFO_MIME);
    $info = finfo_file($r, $file);
    finfo_close($r);
    return preg_replace('/(.*); .*/', '$1', $info);
  }

  static function getExtensionByMime($mime) {
    $mimesText = "application/activemessage
application/andrew-inset      ez
application/applefile
application/atom+xml        atom
application/atomcat+xml       atomcat
application/atomicmail
application/atomsvc+xml       atomsvc
application/auth-policy+xml
application/batch-smtp
application/beep+xml
application/cals-1840
application/ccxml+xml       ccxml
application/cellml+xml
application/cnrp+xml
application/commonground
application/conference-info+xml
application/cpl+xml
application/csta+xml
application/cstadata+xml
application/cybercash
application/davmount+xml      davmount
application/dca-rft
application/dec-dx
application/dialog-info+xml
application/dicom
application/dns
application/dvcs
application/ecmascript        ecma
application/edi-consent
application/edi-x12
application/edifact
application/epp+xml
application/eshop
application/fastinfoset
application/fastsoap
application/fits
application/font-tdpfr        pfr
application/h224
application/http
application/hyperstudio       stk
application/iges
application/im-iscomposing+xml
application/index
application/index.cmd
application/index.obj
application/index.response
application/index.vnd
application/iotp
application/ipp
application/isup
application/javascript        js
application/json        json
application/kpml-request+xml
application/kpml-response+xml
application/mac-binhex40      hqx
application/mac-compactpro      cpt
application/macwriteii
application/marc        mrc
application/mathematica       ma nb mb
application/mathml+xml        mathml
application/mbms-associated-procedure-description+xml
application/mbms-deregister+xml
application/mbms-envelope+xml
application/mbms-msk+xml
application/mbms-msk-response+xml
application/mbms-protection-description+xml
application/mbms-reception-report+xml
application/mbms-register+xml
application/mbms-register-response+xml
application/mbms-user-service-description+xml
application/mbox        mbox
application/mediaservercontrol+xml    mscml
application/mikey
application/moss-keys
application/moss-signature
application/mosskey-data
application/mosskey-request
application/mp4         mp4s
application/mpeg4-generic
application/mpeg4-iod
application/mpeg4-iod-xmt
application/msword        doc dot
application/mxf         mxf
application/nasdata
application/news-message-id
application/news-transmission
application/nss
application/ocsp-request
application/ocsp-response
application/octet-stream bin dms lha lzh class so iso dmg dist distz pkg bpk dump elc
application/oda         oda
application/oebps-package+xml
application/ogg         ogg
application/parityfec
application/pdf         pdf
application/pgp-encrypted     pgp
application/pgp-keys
application/pgp-signature     asc sig
application/pics-rules        prf
application/pidf+xml
application/pkcs10        p10
application/pkcs7-mime        p7m p7c
application/pkcs7-signature     p7s
application/pkix-cert       cer
application/pkix-crl        crl
application/pkix-pkipath      pkipath
application/pkixcmp       pki
application/pls+xml       pls
application/poc-settings+xml
application/postscript        ai eps ps
application/prs.alvestrand.titrax-sheet
application/prs.cww       cww
application/prs.nprend
application/prs.plucker
application/qsig
application/rdf+xml       rdf
application/reginfo+xml       rif
application/relax-ng-compact-syntax   rnc
application/remote-printing
application/resource-lists+xml      rl
application/riscos
application/rlmi+xml
application/rls-services+xml      rs
application/rsd+xml       rsd
application/rss+xml       rss
application/rtf         rtf
application/rtx
application/samlassertion+xml
application/samlmetadata+xml
application/sbml+xml        sbml
application/scvp-cv-request     scq
application/scvp-cv-response      scs
application/scvp-vp-request     spq
application/scvp-vp-response      spp
application/sdp         sdp
application/set-payment
application/set-payment-initiation    setpay
application/set-registration
application/set-registration-initiation   setreg
application/sgml
application/sgml-open-catalog
application/shf+xml       shf
application/sieve
application/simple-filter+xml
application/simple-message-summary
application/simplesymbolcontainer
application/slate
application/smil
application/smil+xml        smi smil
application/soap+fastinfoset
application/soap+xml
application/sparql-query      rq
application/sparql-results+xml      srx
application/spirits-event+xml
application/srgs        gram
application/srgs+xml        grxml
application/ssml+xml        ssml
application/timestamp-query
application/timestamp-reply
application/tve-trigger
application/ulpfec
application/vemmi
application/vividence.scriptfile
application/vnd.3gpp.bsf+xml
application/vnd.3gpp.pic-bw-large   plb
application/vnd.3gpp.pic-bw-small   psb
application/vnd.3gpp.pic-bw-var     pvb
application/vnd.3gpp.sms
application/vnd.3gpp2.bcmcsinfo+xml
application/vnd.3gpp2.sms
application/vnd.3gpp2.tcap      tcap
application/vnd.3m.post-it-notes    pwn
application/vnd.accpac.simply.aso   aso
application/vnd.accpac.simply.imp   imp
application/vnd.acucobol      acu
application/vnd.acucorp       atc acutc
application/vnd.adobe.xdp+xml     xdp
application/vnd.adobe.xfdf      xfdf
application/vnd.aether.imp
application/vnd.amiga.ami     ami
application/vnd.anser-web-certificate-issue-initiation  cii
application/vnd.anser-web-funds-transfer-initiation fti
application/vnd.antix.game-component    atx
application/vnd.apple.installer+xml   mpkg
application/vnd.audiograph      aep
application/vnd.autopackage
application/vnd.avistar+xml
application/vnd.blueice.multipass   mpm
application/vnd.bmi       bmi
application/vnd.businessobjects     rep
application/vnd.cab-jscript
application/vnd.canon-cpdl
application/vnd.canon-lips
application/vnd.cendio.thinlinc.clientconf
application/vnd.chemdraw+xml      cdxml
application/vnd.chipnuts.karaoke-mmd    mmd
application/vnd.cinderella      cdy
application/vnd.cirpack.isdn-ext
application/vnd.claymore      cla
application/vnd.clonk.c4group     c4g c4d c4f c4p c4u
application/vnd.commerce-battelle
application/vnd.commonspace     csp cst
application/vnd.contact.cmsg      cdbcmsg
application/vnd.cosmocaller     cmc
application/vnd.crick.clicker     clkx
application/vnd.crick.clicker.keyboard    clkk
application/vnd.crick.clicker.palette   clkp
application/vnd.crick.clicker.template    clkt
application/vnd.crick.clicker.wordbank    clkw
application/vnd.criticaltools.wbs+xml   wbs
application/vnd.ctc-posml     pml
application/vnd.cups-pdf
application/vnd.cups-postscript
application/vnd.cups-ppd      ppd
application/vnd.cups-raster
application/vnd.cups-raw
application/vnd.curl        curl
application/vnd.cybank
application/vnd.data-vision.rdz     rdz
application/vnd.denovo.fcselayout-link    fe_launch
application/vnd.dna       dna
application/vnd.dolby.mlp     mlp
application/vnd.dpgraph       dpg
application/vnd.dreamfactory      dfac
application/vnd.dvb.esgcontainer
application/vnd.dvb.ipdcesgaccess
application/vnd.dxr
application/vnd.ecdis-update
application/vnd.ecowin.chart      mag
application/vnd.ecowin.filerequest
application/vnd.ecowin.fileupdate
application/vnd.ecowin.series
application/vnd.ecowin.seriesrequest
application/vnd.ecowin.seriesupdate
application/vnd.enliven       nml
application/vnd.epson.esf     esf
application/vnd.epson.msf     msf
application/vnd.epson.quickanime    qam
application/vnd.epson.salt      slt
application/vnd.epson.ssf     ssf
application/vnd.ericsson.quickcall
application/vnd.eszigno3+xml      es3 et3
application/vnd.eudora.data
application/vnd.ezpix-album     ez2
application/vnd.ezpix-package     ez3
application/vnd.fdf       fdf
application/vnd.ffsns
application/vnd.fints
application/vnd.flographit      gph
application/vnd.fluxtime.clip     ftc
application/vnd.framemaker      fm frame maker
application/vnd.frogans.fnc     fnc
application/vnd.frogans.ltf     ltf
application/vnd.fsc.weblaunch     fsc
application/vnd.fujitsu.oasys     oas
application/vnd.fujitsu.oasys2      oa2
application/vnd.fujitsu.oasys3      oa3
application/vnd.fujitsu.oasysgp     fg5
application/vnd.fujitsu.oasysprs    bh2
application/vnd.fujixerox.art-ex
application/vnd.fujixerox.art4
application/vnd.fujixerox.hbpl
application/vnd.fujixerox.ddd     ddd
application/vnd.fujixerox.docuworks   xdw
application/vnd.fujixerox.docuworks.binder  xbd
application/vnd.fut-misnet
application/vnd.fuzzysheet      fzs
application/vnd.genomatix.tuxedo    txd
application/vnd.google-earth.kml+xml    kml
application/vnd.google-earth.kmz    kmz
application/vnd.grafeq        gqf gqs
application/vnd.gridmp
application/vnd.groove-account      gac
application/vnd.groove-help     ghf
application/vnd.groove-identity-message   gim
application/vnd.groove-injector     grv
application/vnd.groove-tool-message   gtm
application/vnd.groove-tool-template    tpl
application/vnd.groove-vcard      vcg
application/vnd.handheld-entertainment+xml  zmm
application/vnd.hbci        hbci
application/vnd.hcl-bireports
application/vnd.hhe.lesson-player   les
application/vnd.hp-hpgl       hpgl
application/vnd.hp-hpid       hpid
application/vnd.hp-hps        hps
application/vnd.hp-jlyt       jlt
application/vnd.hp-pcl        pcl
application/vnd.hp-pclxl      pclxl
application/vnd.httphone
application/vnd.hzn-3d-crossword    x3d
application/vnd.ibm.afplinedata
application/vnd.ibm.electronic-media
application/vnd.ibm.minipay     mpy
application/vnd.ibm.modcap      afp listafp list3820
application/vnd.ibm.rights-management   irm
application/vnd.ibm.secure-container    sc
application/vnd.igloader      igl
application/vnd.immervision-ivp     ivp
application/vnd.immervision-ivu     ivu
application/vnd.informedcontrol.rms+xml
application/vnd.intercon.formnet    xpw xpx
application/vnd.intertrust.digibox
application/vnd.intertrust.nncp
application/vnd.intu.qbo      qbo
application/vnd.intu.qfx      qfx
application/vnd.ipunplugged.rcprofile   rcprofile
application/vnd.irepository.package+xml   irp
application/vnd.is-xpr        xpr
application/vnd.jam       jam
application/vnd.japannet-directory-service
application/vnd.japannet-jpnstore-wakeup
application/vnd.japannet-payment-wakeup
application/vnd.japannet-registration
application/vnd.japannet-registration-wakeup
application/vnd.japannet-setstore-wakeup
application/vnd.japannet-verification
application/vnd.japannet-verification-wakeup
application/vnd.jcp.javame.midlet-rms   rms
application/vnd.jisp        jisp
application/vnd.joost.joda-archive    joda
application/vnd.kahootz       ktz ktr
application/vnd.kde.karbon      karbon
application/vnd.kde.kchart      chrt
application/vnd.kde.kformula      kfo
application/vnd.kde.kivio     flw
application/vnd.kde.kontour     kon
application/vnd.kde.kpresenter      kpr kpt
application/vnd.kde.kspread     ksp
application/vnd.kde.kword     kwd kwt
application/vnd.kenameaapp      htke
application/vnd.kidspiration      kia
application/vnd.kinar       kne knp
application/vnd.koan        skp skd skt skm
application/vnd.liberty-request+xml
application/vnd.llamagraphics.life-balance.desktop  lbd
application/vnd.llamagraphics.life-balance.exchange+xml lbe
application/vnd.lotus-1-2-3     123
application/vnd.lotus-approach      apr
application/vnd.lotus-freelance     pre
application/vnd.lotus-notes     nsf
application/vnd.lotus-organizer     org
application/vnd.lotus-screencam     scm
application/vnd.lotus-wordpro     lwp
application/vnd.macports.portpkg    portpkg
application/vnd.marlin.drm.actiontoken+xml
application/vnd.marlin.drm.conftoken+xml
application/vnd.marlin.drm.mdcf
application/vnd.mcd       mcd
application/vnd.medcalcdata     mc1
application/vnd.mediastation.cdkey    cdkey
application/vnd.meridian-slingshot
application/vnd.mfer        mwf
application/vnd.mfmp        mfm
application/vnd.micrografx.flo      flo
application/vnd.micrografx.igx      igx
application/vnd.mif       mif
application/vnd.minisoft-hp3000-save
application/vnd.mitsubishi.misty-guard.trustweb
application/vnd.mobius.daf      daf
application/vnd.mobius.dis      dis
application/vnd.mobius.mbk      mbk
application/vnd.mobius.mqy      mqy
application/vnd.mobius.msl      msl
application/vnd.mobius.plc      plc
application/vnd.mobius.txf      txf
application/vnd.mophun.application    mpn
application/vnd.mophun.certificate    mpc
application/vnd.motorola.flexsuite
application/vnd.motorola.flexsuite.adsi
application/vnd.motorola.flexsuite.fis
application/vnd.motorola.flexsuite.gotap
application/vnd.motorola.flexsuite.kmr
application/vnd.motorola.flexsuite.ttc
application/vnd.motorola.flexsuite.wem
application/vnd.mozilla.xul+xml xul
application/vnd.ms-artgalry     cil
application/vnd.ms-asf        asf
application/vnd.ms-cab-compressed   cab
application/vnd.ms-excel      xls xlm xla xlc xlt xlw
application/vnd.ms-fontobject     eot
application/vnd.ms-htmlhelp     chm
application/vnd.ms-ims        ims
application/vnd.ms-lrm        lrm
application/vnd.ms-playready.initiator+xml
application/vnd.ms-powerpoint     ppt pps pot
application/vnd.ms-project      mpp mpt
application/vnd.ms-tnef
application/vnd.ms-wmdrm.lic-chlg-req
application/vnd.ms-wmdrm.lic-resp
application/vnd.ms-wmdrm.meter-chlg-req
application/vnd.ms-wmdrm.meter-resp
application/vnd.ms-works      wps wks wcm wdb
application/vnd.ms-wpl        wpl
application/vnd.ms-xpsdocument      xps
application/vnd.mseq        mseq
application/vnd.msign
application/vnd.multiad.creator
application/vnd.multiad.creator.cif
application/vnd.music-niff
application/vnd.musician      mus
application/vnd.muvee.style     msty
application/vnd.ncd.control
application/vnd.ncd.reference
application/vnd.nervana
application/vnd.netfpx
application/vnd.neurolanguage.nlu   nlu
application/vnd.noblenet-directory    nnd
application/vnd.noblenet-sealer     nns
application/vnd.noblenet-web      nnw
application/vnd.nokia.catalogs
application/vnd.nokia.conml+wbxml
application/vnd.nokia.conml+xml
application/vnd.nokia.isds-radio-presets
application/vnd.nokia.iptv.config+xml
application/vnd.nokia.landmark+wbxml
application/vnd.nokia.landmark+xml
application/vnd.nokia.landmarkcollection+xml
application/vnd.nokia.n-gage.ac+xml
application/vnd.nokia.n-gage.data   ngdat
application/vnd.nokia.n-gage.symbian.install  n-gage
application/vnd.nokia.ncd
application/vnd.nokia.pcd+wbxml
application/vnd.nokia.pcd+xml
application/vnd.nokia.radio-preset    rpst
application/vnd.nokia.radio-presets   rpss
application/vnd.novadigm.edm      edm
application/vnd.novadigm.edx      edx
application/vnd.novadigm.ext      ext
application/vnd.oasis.opendocument.chart    odc
application/vnd.oasis.opendocument.chart-template otc
application/vnd.oasis.opendocument.formula    odf
application/vnd.oasis.opendocument.formula-template otf
application/vnd.oasis.opendocument.graphics   odg
application/vnd.oasis.opendocument.graphics-template  otg
application/vnd.oasis.opendocument.image    odi
application/vnd.oasis.opendocument.image-template oti
application/vnd.oasis.opendocument.presentation   odp
application/vnd.oasis.opendocument.presentation-template otp
application/vnd.oasis.opendocument.spreadsheet    ods
application/vnd.oasis.opendocument.spreadsheet-template ots
application/vnd.oasis.opendocument.text     odt
application/vnd.oasis.opendocument.text-master    otm
application/vnd.oasis.opendocument.text-template  ott
application/vnd.oasis.opendocument.text-web   oth
application/vnd.obn
application/vnd.olpc-sugar      xo
application/vnd.oma-scws-config
application/vnd.oma-scws-http-request
application/vnd.oma-scws-http-response
application/vnd.oma.bcast.associated-procedure-parameter+xml
application/vnd.oma.bcast.drm-trigger+xml
application/vnd.oma.bcast.imd+xml
application/vnd.oma.bcast.notification+xml
application/vnd.oma.bcast.sgboot
application/vnd.oma.bcast.sgdd+xml
application/vnd.oma.bcast.sgdu
application/vnd.oma.bcast.simple-symbol-container
application/vnd.oma.bcast.smartcard-trigger+xml
application/vnd.oma.bcast.sprov+xml
application/vnd.oma.dd2+xml     dd2
application/vnd.oma.drm.risd+xml
application/vnd.oma.group-usage-list+xml
application/vnd.oma.poc.detailed-progress-report+xml
application/vnd.oma.poc.final-report+xml
application/vnd.oma.poc.groups+xml
application/vnd.oma.poc.optimized-progress-report+xml
application/vnd.oma.xcap-directory+xml
application/vnd.omads-email+xml
application/vnd.omads-file+xml
application/vnd.omads-folder+xml
application/vnd.omaloc-supl-init
application/vnd.openofficeorg.extension   oxt
application/vnd.osa.netdeploy
application/vnd.osgi.dp       dp
application/vnd.otps.ct-kip+xml
application/vnd.palm        prc pdb pqa oprc
application/vnd.paos.xml
application/vnd.pg.format     str
application/vnd.pg.osasli     ei6
application/vnd.piaccess.application-licence
application/vnd.picsel        efif
application/vnd.poc.group-advertisement+xml
application/vnd.pocketlearn     plf
application/vnd.powerbuilder6     pbd
application/vnd.powerbuilder6-s
application/vnd.powerbuilder7
application/vnd.powerbuilder7-s
application/vnd.powerbuilder75
application/vnd.powerbuilder75-s
application/vnd.preminet
application/vnd.previewsystems.box    box
application/vnd.proteus.magazine    mgz
application/vnd.publishare-delta-tree   qps
application/vnd.pvi.ptid1     ptid
application/vnd.pwg-multiplexed
application/vnd.pwg-xhtml-print+xml
application/vnd.qualcomm.brew-app-res
application/vnd.quark.quarkxpress   qxd qxt qwd qwt qxl qxb
application/vnd.rapid
application/vnd.recordare.musicxml    mxl
application/vnd.recordare.musicxml+xml
application/vnd.renlearn.rlprint
application/vnd.rn-realmedia      rm
application/vnd.ruckus.download
application/vnd.s3sms
application/vnd.sbm.mid2
application/vnd.scribus
application/vnd.sealed.3df
application/vnd.sealed.csf
application/vnd.sealed.doc
application/vnd.sealed.eml
application/vnd.sealed.mht
application/vnd.sealed.net
application/vnd.sealed.ppt
application/vnd.sealed.tiff
application/vnd.sealed.xls
application/vnd.sealedmedia.softseal.html
application/vnd.sealedmedia.softseal.pdf
application/vnd.seemail       see
application/vnd.sema        sema
application/vnd.semd        semd
application/vnd.semf        semf
application/vnd.shana.informed.formdata   ifm
application/vnd.shana.informed.formtemplate itp
application/vnd.shana.informed.interchange  iif
application/vnd.shana.informed.package    ipk
application/vnd.simtech-mindmapper    twd twds
application/vnd.smaf        mmf
application/vnd.solent.sdkm+xml     sdkm sdkd
application/vnd.spotfire.dxp      dxp
application/vnd.spotfire.sfs      sfs
application/vnd.sss-cod
application/vnd.sss-dtf
application/vnd.sss-ntf
application/vnd.street-stream
application/vnd.sun.wadl+xml
application/vnd.sus-calendar      sus susp
application/vnd.svd       svd
application/vnd.swiftview-ics
application/vnd.syncml+xml      xsm
application/vnd.syncml.dm+wbxml     bdm
application/vnd.syncml.dm+xml     xdm
application/vnd.syncml.ds.notification
application/vnd.tao.intent-module-archive tao
application/vnd.tmobile-livetv      tmo
application/vnd.trid.tpt      tpt
application/vnd.triscape.mxs      mxs
application/vnd.trueapp       tra
application/vnd.truedoc
application/vnd.ufdl        ufd ufdl
application/vnd.uiq.theme     utz
application/vnd.umajin        umj
application/vnd.unity       unityweb
application/vnd.uoml+xml      uoml
application/vnd.uplanet.alert
application/vnd.uplanet.alert-wbxml
application/vnd.uplanet.bearer-choice
application/vnd.uplanet.bearer-choice-wbxml
application/vnd.uplanet.cacheop
application/vnd.uplanet.cacheop-wbxml
application/vnd.uplanet.channel
application/vnd.uplanet.channel-wbxml
application/vnd.uplanet.list
application/vnd.uplanet.list-wbxml
application/vnd.uplanet.listcmd
application/vnd.uplanet.listcmd-wbxml
application/vnd.uplanet.signal
application/vnd.vcx       vcx
application/vnd.vd-study
application/vnd.vectorworks
application/vnd.vidsoft.vidconference
application/vnd.visio       vsd vst vss vsw
application/vnd.visionary     vis
application/vnd.vividence.scriptfile
application/vnd.vsf       vsf
application/vnd.wap.sic
application/vnd.wap.slc
application/vnd.wap.wbxml     wbxml
application/vnd.wap.wmlc      wmlc
application/vnd.wap.wmlscriptc      wmlsc
application/vnd.webturbo      wtb
application/vnd.wfa.wsc
application/vnd.wmc
application/vnd.wordperfect     wpd
application/vnd.wqd       wqd
application/vnd.wrq-hp3000-labelled
application/vnd.wt.stf        stf
application/vnd.wv.csp+wbxml
application/vnd.wv.csp+xml
application/vnd.wv.ssp+xml
application/vnd.xara        xar
application/vnd.xfdl        xfdl
application/vnd.xmpie.cpkg
application/vnd.xmpie.dpkg
application/vnd.xmpie.plan
application/vnd.xmpie.ppkg
application/vnd.xmpie.xlim
application/vnd.yamaha.hv-dic     hvd
application/vnd.yamaha.hv-script    hvs
application/vnd.yamaha.hv-voice     hvp
application/vnd.yamaha.smaf-audio   saf
application/vnd.yamaha.smaf-phrase    spf
application/vnd.yellowriver-custom-menu   cmp
application/vnd.zzazz.deck+xml      zaz
application/voicexml+xml      vxml
application/watcherinfo+xml
application/whoispp-query
application/whoispp-response
application/winhlp        hlp
application/wita
application/wordperfect5.1
application/wsdl+xml        wsdl
application/wspolicy+xml      wspolicy
application/x-ace-compressed      ace
application/x-bcpio       bcpio
application/x-bittorrent      torrent
application/x-bzip        bz
application/x-bzip2       bz2 boz
application/x-cdlink        vcd
application/x-chat        chat
application/x-chess-pgn       pgn
application/x-compress
application/x-cpio        cpio
application/x-csh       csh
application/x-director        dcr dir dxr fgd
application/x-dvi       dvi
application/x-futuresplash      spl
application/x-gtar        gtar
application/x-gzip
application/x-hdf       hdf
application/x-latex       latex
application/x-ms-wmd        wmd
application/x-ms-wmz        wmz
application/x-msaccess        mdb
application/x-msbinder        obd
application/x-mscardfile      crd
application/x-msclip        clp
application/x-msdownload      exe dll com bat msi
application/x-msmediaview     mvb m13 m14
application/x-msmetafile      wmf
application/x-msmoney       mny
application/x-mspublisher     pub
application/x-msschedule      scd
application/x-msterminal      trm
application/x-mswrite       wri
application/x-netcdf        nc cdf
application/x-pkcs12        p12 pfx
application/x-pkcs7-certificates    p7b spc
application/x-pkcs7-certreqresp     p7r
application/x-rar-compressed      rar
application/x-sh        sh
application/x-shar        shar
application/x-shockwave-flash     swf
application/x-stuffit       sit
application/x-stuffitx        sitx
application/x-sv4cpio       sv4cpio
application/x-sv4crc        sv4crc
application/x-tar       tar
application/x-tcl       tcl
application/x-tex       tex
application/x-texinfo       texinfo texi
application/x-ustar       ustar
application/x-wais-source     src
application/x-x509-ca-cert      der crt
application/x400-bp
application/xcap-att+xml
application/xcap-caps+xml
application/xcap-el+xml
application/xcap-error+xml
application/xcap-ns+xml
application/xenc+xml        xenc
application/xhtml+xml       xhtml xht
application/xml         xml xsl
application/xml-dtd       dtd
application/xml-external-parsed-entity
application/xmpp+xml
application/xop+xml       xop
application/xslt+xml        xslt
application/xspf+xml        xspf
application/xv+xml        mxml xhvml xvml xvm
application/zip         zip
audio/32kadpcm
audio/3gpp
audio/3gpp2
audio/ac3
audio/amr
audio/amr-wb
audio/amr-wb+
audio/asc
audio/basic         au snd
audio/bv16
audio/bv32
audio/clearmode
audio/cn
audio/dat12
audio/dls
audio/dsr-es201108
audio/dsr-es202050
audio/dsr-es202211
audio/dsr-es202212
audio/dvi4
audio/eac3
audio/evrc
audio/evrc-qcp
audio/evrc0
audio/evrc1
audio/evrcb
audio/evrcb0
audio/evrcb1
audio/g722
audio/g7221
audio/g723
audio/g726-16
audio/g726-24
audio/g726-32
audio/g726-40
audio/g728
audio/g729
audio/g7291
audio/g729d
audio/g729e
audio/gsm
audio/gsm-efr
audio/ilbc
audio/l16
audio/l20
audio/l24
audio/l8
audio/lpc
audio/midi          mid
audio/mobile-xmf
audio/mp4         mp4a
audio/mp4a-latm
audio/mpa
audio/mpa-robust
audio/mpeg          mp3
audio/mpeg4-generic
audio/parityfec
audio/pcma
audio/pcmu
audio/prs.sid
audio/qcelp
audio/red
audio/rtp-enc-aescm128
audio/rtp-midi
audio/rtx
audio/smv
audio/smv0
audio/smv-qcp
audio/sp-midi
audio/t140c
audio/t38
audio/telephone-event
audio/tone
audio/ulpfec
audio/vdvi
audio/vmr-wb
audio/vnd.3gpp.iufp
audio/vnd.4sb
audio/vnd.audiokoz
audio/vnd.celp
audio/vnd.cisco.nse
audio/vnd.cmles.radio-events
audio/vnd.cns.anp1
audio/vnd.cns.inf1
audio/vnd.digital-winds       eol
audio/vnd.dlna.adts
audio/vnd.dolby.mlp
audio/vnd.everad.plj
audio/vnd.hns.audio
audio/vnd.lucent.voice        lvp
audio/vnd.nokia.mobile-xmf
audio/vnd.nortel.vbk
audio/vnd.nuera.ecelp4800     ecelp4800
audio/vnd.nuera.ecelp7470     ecelp7470
audio/vnd.nuera.ecelp9600     ecelp9600
audio/vnd.octel.sbc
audio/vnd.qcelp
audio/vnd.rhetorex.32kadpcm
audio/vnd.sealedmedia.softseal.mpeg
audio/vnd.vmx.cvsd
audio/wav         wav
audio/x-aiff          aif aiff aifc
audio/x-mpegurl         m3u
audio/x-ms-wax          wax
audio/x-ms-wma          wma
audio/x-pn-realaudio        ram ra
audio/x-pn-realaudio-plugin     rmp
audio/x-wav         wav
chemical/x-cdx          cdx
chemical/x-cif          cif
chemical/x-cmdf         cmdf
chemical/x-cml          cml
chemical/x-csml         csml
chemical/x-pdb          pdb
chemical/x-xyz          xyz
image/bmp         bmp
image/cgm         cgm
image/fits
image/g3fax         g3
image/gif         gif
image/ief         ief
image/jp2
image/jpeg          jpg
image/jpm
image/jpx
image/naplps
image/png         png
image/prs.btif          btif
image/prs.pti
image/svg+xml         svg svgz
image/t38
image/tiff          tiff tif
image/tiff-fx
image/vnd.adobe.photoshop     psd
image/vnd.cns.inf2
image/vnd.djvu          djvu djv
image/vnd.dwg         dwg
image/vnd.dxf         dxf
image/vnd.fastbidsheet        fbs
image/vnd.fpx         fpx
image/vnd.fst         fst
image/vnd.fujixerox.edmics-mmr      mmr
image/vnd.fujixerox.edmics-rlc      rlc
image/vnd.globalgraphics.pgb
image/vnd.microsoft.icon
image/vnd.mix
image/vnd.ms-modi       mdi
image/vnd.net-fpx       npx
image/vnd.sealed.png
image/vnd.sealedmedia.softseal.gif
image/vnd.sealedmedia.softseal.jpg
image/vnd.svf
image/vnd.wap.wbmp        wbmp
image/vnd.xiff          xif
image/x-cmu-raster        ras
image/x-cmx         cmx
image/x-icon          ico
image/x-pcx         pcx
image/x-pict          pic pct
image/x-portable-anymap       pnm
image/x-portable-bitmap       pbm
image/x-portable-graymap      pgm
image/x-portable-pixmap       ppm
image/x-rgb         rgb
image/x-xbitmap         xbm
image/x-xpixmap         xpm
image/x-xwindowdump       xwd
message/cpim
message/delivery-status
message/disposition-notification
message/external-body
message/http
message/news
message/partial
message/rfc822          eml mime
message/s-http
message/sip
message/sipfrag
message/tracking-status
message/vnd.si.simp
model/iges          igs iges
model/mesh          msh mesh silo
model/vnd.dwf         dwf
model/vnd.flatland.3dml
model/vnd.gdl         gdl
model/vnd.gs.gdl
model/vnd.gtw         gtw
model/vnd.moml+xml
model/vnd.mts         mts
model/vnd.parasolid.transmit.binary
model/vnd.parasolid.transmit.text
model/vnd.vtu         vtu
model/vrml          wrl vrml
multipart/alternative
multipart/appledouble
multipart/byteranges
multipart/digest
multipart/encrypted
multipart/form-data
multipart/header-set
multipart/mixed
multipart/parallel
multipart/related
multipart/report
multipart/signed
multipart/voice-message
text/calendar         ics ifb
text/css          css
text/csv          csv
text/directory
text/dns
text/enriched
text/html         html htm
text/parityfec
text/plain          txt text conf def list log in
text/prs.fallenstein.rst
text/prs.lines.tag        dsc
text/red
text/rfc822-headers
text/richtext         rtx
text/rtf
text/rtp-enc-aescm128
text/rtx
text/sgml         sgml sgm
text/t140
text/tab-separated-values     tsv
text/troff          t tr roff man me ms
text/ulpfec
text/uri-list         uri uris urls
text/vnd.abc
text/vnd.curl
text/vnd.dmclientscript
text/vnd.esmertec.theme-descriptor
text/vnd.fly          fly
text/vnd.fmi.flexstor       flx
text/vnd.in3d.3dml        3dml
text/vnd.in3d.spot        spot
text/vnd.iptc.newsml
text/vnd.iptc.nitf
text/vnd.latex-z
text/vnd.motorola.reflex
text/vnd.ms-mediapackage
text/vnd.net2phone.commcenter.command
text/vnd.si.uricatalogue
text/vnd.sun.j2me.app-descriptor    jad
text/vnd.trolltech.linguist
text/vnd.wap.si
text/vnd.wap.sl
text/vnd.wap.wml        wml
text/vnd.wap.wmlscript        wmls
text/x-asm          s asm
text/x-c          c cc cxx cpp h hh dic
text/x-fortran          f for f77 f90
text/x-pascal         p pas
text/x-java-source        java
text/x-setext         etx
text/x-uuencode         uu
text/x-vcalendar        vcs
text/x-vcard          vcf
text/xml
text/xml-external-parsed-entity
video/3gpp          3gp
video/3gpp-tt
video/3gpp2         3g2
video/bmpeg
video/bt656
video/celb
video/dv
video/h261          h261
video/h263          h263
video/h263-1998
video/h263-2000
video/h264          mpg
video/jpeg          jpgv
video/jpm         jpm jpgm
video/mj2         mj2 mjp2
video/mp1s
video/mp2p
video/mp2t
video/mp4         mpg
video/mp4v-es
video/mpeg          mpg
video/mpeg4-generic
video/mpv
video/nv
video/parityfec
video/pointer
video/quicktime         mov
video/vnd.fvt         fvt
video/vnd.mpegurl       mxu m4u
video/vnd.nokia.interleaved-multimedia
video/vnd.sealedmedia.softseal.mov
video/vnd.vivo          viv
video/x-fli         fli
video/x-ms-asf          asf  asx
video/x-ms-wm         wm
video/x-ms-wmv          wmv
video/x-ms-wmx          wmx
video/x-ms-wvx          wvx
video/x-msvideo         avi
video/x-sgi-movie       movie
x-conference/x-cooltalk         ice
application/x-httpd-php         php
application/x-httpd-php-source  phps
    ";
    preg_match_all('/^([\w\/\-]+)(?=\s+)(.*)$/Um', $mimesText, $m);
    for ($i = 0; $i < count($m[1]); $i++) {
      if ($ext = trim($m[2][$i])) { // Если есть расширение для этого MIME
        $_mime = trim($m[1][$i]);
        if ($_mime == $mime) {
          $ext = preg_replace('/\s+/', ' ', $ext);
          $exts = explode(' ', $ext);
          return $exts[0];
        }
      }
    }
    return false;
  }

  const SIZE_KB = 1024;
  const SIZE_MB = 1048576;
  const SIZE_GB = 1073741824;

  static function format($dsize) {
    if (strlen($dsize) <= 9 && strlen($dsize) >= 7) {
      return number_format($dsize / self::SIZE_MB, 1).' Мб';
    }
    elseif (strlen($dsize) >= 10) {
      return number_format($dsize / self::SIZE_GB, 1).' Гб';
    }
    else {
      return number_format($dsize / self::SIZE_KB, 1).' Кб';
    }
  }

  static function format2($sizeInBytes, $l = 0, $precision = 2) {
    static $label = [' Кб', ' Мб', ' Гб'];
    $val = $sizeInBytes / 1024;
    if (floor($val) >= 1024) {
      $l++;
      $val = self::format2(floor($val), $l);
    }
    return round($val, $precision).$label[$l];
  }

  static function copy($from, $to) {
    copy($from, $to);
    output('Copy "'.$from.'" ---> "'.$to.'"');
  }

  static function copyToFolder($file, $folder) {
    copy($file, $folder.'/'.basename($file));
  }

  static function checkExists($file, $exceptionText = null) {
    if (!file_exists($file)) throw new Exception($exceptionText ? : "File '$file' does not exists");
    return $file;
  }

  static function prepend($file, $contents) {
    file_put_contents($file, $contents.file_get_contents($file));
  }

  static function findContents($file, $find) {
    return strstr(file_get_contents($file), $find);
  }

  static function replace($file, $from, $to) {
    file_put_contents($file, str_replace($from, $to, file_get_contents($file)));
  }

  static function replaceTttt($file, array $data) {
    file_put_contents($file, St::tttt(file_get_contents($file), $data));
  }

  static function relative($path, $compareTo) {
    // clean arguments by removing trailing and prefixing slashes
    if (substr($path, -1) == '/') {
      $path = substr($path, 0, -1);
    }
    if (substr($path, 0, 1) == '/') {
      $path = substr($path, 1);
    }
    if (substr($compareTo, -1) == '/') {
      $compareTo = substr($compareTo, 0, -1);
    }
    if (substr($compareTo, 0, 1) == '/') {
      $compareTo = substr($compareTo, 1);
    }
    // simple case: $compareTo is in $path
    if (strpos($path, $compareTo) === 0) {
      $offset = strlen($compareTo) + 1;
      return substr($path, $offset);
    }
    $relative = [];
    $pathParts = explode('/', $path);
    $compareToParts = explode('/', $compareTo);
    foreach ($compareToParts as $index => $part) {
      if (isset($pathParts[$index]) && $pathParts[$index] == $part) {
        continue;
      }
      $relative[] = '..';
    }
    foreach ($pathParts as $index => $part) {
      if (isset($compareToParts[$index]) && $compareToParts[$index] == $part) {
        continue;
      }
      $relative[] = $part;
    }
    return implode('/', $relative);
  }

  static function lines($file) {
    return array_map('trim', file($file));
  }

  static $phpIniToHuman = [
    'M' => 'Мб'
  ];

}
