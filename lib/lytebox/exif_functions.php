<?php
/*
    This file is a part of DAlbum.  Copyright (c) 2003 Alexei Shamov, DeltaX Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


    This code is based on

    //--------------------------------------------------------------------------
    // Program to pull the information out of various types of EXIF digital
    // camera files and show it in a reasonably consistent way
    //
    // This module parses the very complicated exif structures.
    //
    // Matthias Wandel,  Dec 1999 - Dec 2002
    //--------------------------------------------------------------------------
    */

//--------------------------------------------------------------------------
// Describes format descriptor


define("NUM_FORMATS",   12);
define("FMT_BYTE",       1);
define("FMT_STRING",     2);
define("FMT_USHORT",     3);
define("FMT_ULONG",      4);
define("FMT_URATIONAL",  5);
define("FMT_SBYTE",      6);
define("FMT_UNDEFINED",  7);
define("FMT_SSHORT",     8);
define("FMT_SLONG",      9);
define("FMT_SRATIONAL", 10);
define("FMT_SINGLE",    11);
define("FMT_DOUBLE",    12);

//--------------------------------------------------------------------------
// Describes tag values

define("TAG_EXIF_OFFSET",       0x8769);
define("TAG_INTEROP_OFFSET",    0xa005);
define("TAG_MAKE",              0x010F);
define("TAG_MODEL",             0x0110);
define("TAG_ORIENTATION",       0x0112);
define("TAG_EXPOSURETIME",      0x829A);
define("TAG_FNUMBER",           0x829D);
define("TAG_SHUTTERSPEED",      0x9201);
define("TAG_APERTURE",          0x9202);
define("TAG_MAXAPERTURE",       0x9205);
define("TAG_FOCALLENGTH",       0x920A);
define("TAG_DATETIME_ORIGINAL", 0x9003);
define("TAG_USERCOMMENT",       0x9286);
define("TAG_SUBJECT_DISTANCE",  0x9206);
define("TAG_FLASH",             0x9209);
define("TAG_FOCALPLANEXRES",    0xa20E);
define("TAG_FOCALPLANEUNITS",   0xa210);
define("TAG_EXIF_IMAGEWIDTH",   0xA002);
define("TAG_EXIF_IMAGELENGTH",  0xA003);

// the following is added 05-jan-2001 vcs
define("TAG_EXPOSURE_BIAS",     0x9204);
define("TAG_WHITEBALANCE",      0x9208);
define("TAG_METERING_MODE",     0x9207);
define("TAG_EXPOSURE_PROGRAM",  0x8822);
define("TAG_ISO_EQUIVALENT",    0x8827);
define("TAG_THUMBNAIL_OFFSET",  0x0201);
define("TAG_THUMBNAIL_LENGTH",  0x0202);

$_g_exifTagTable = array(
  0x000B => "ACDComment",
  0x00FE => "NewSubFile", /* better name it 'ImageType' ? */
  0x00FF => "SubFile",
  0x0100 => "ImageWidth",
  0x0101 => "ImageLength",
  0x0102 => "BitsPerSample",
  0x0103 => "Compression",
  0x0106 => "PhotometricInterpretation",
  0x010A => "FillOrder",
  0x010D => "DocumentName",
  0x010E => "ImageDescription",
  0x010F => "Make",
  0x0110 => "Model",
  0x0111 => "StripOffsets",
  0x0112 => "Orientation",
  0x0115 => "SamplesPerPixel",
  0x0116 => "RowsPerStrip",
  0x0117 => "StripByteCounts",
  0x0118 => "MinSampleValue",
  0x0119 => "MaxSampleValue",
  0x011A => "XResolution",
  0x011B => "YResolution",
  0x011C => "PlanarConfiguration",
  0x011D => "PageName",
  0x011E => "XPosition",
  0x011F => "YPosition",
  0x0120 => "FreeOffsets",
  0x0121 => "FreeByteCounts",
  0x0122 => "GrayResponseUnit",
  0x0123 => "GrayResponseCurve",
  0x0124 => "T4Options",
  0x0125 => "T6Options",
  0x0128 => "ResolutionUnit",
  0x0129 => "PageNumber",
  0x012D => "TransferFunction",
  0x0131 => "Software",
  0x0132 => "DateTime",
  0x013B => "Artist",
  0x013C => "HostComputer",
  0x013D => "Predictor",
  0x013E => "WhitePoint",
  0x013F => "PrimaryChromaticities",
  0x0140 => "ColorMap",
  0x0141 => "HalfToneHints",
  0x0142 => "TileWidth",
  0x0143 => "TileLength",
  0x0144 => "TileOffsets",
  0x0145 => "TileByteCounts",
  0x014A => "SubIFD",
  0x014C => "InkSet",
  0x014D => "InkNames",
  0x014E => "NumberOfInks",
  0x0150 => "DotRange",
  0x0151 => "TargetPrinter",
  0x0152 => "ExtraSample",
  0x0153 => "SampleFormat",
  0x0154 => "SMinSampleValue",
  0x0155 => "SMaxSampleValue",
  0x0156 => "TransferRange",
  0x0157 => "ClipPath",
  0x0158 => "XClipPathUnits",
  0x0159 => "YClipPathUnits",
  0x015A => "Indexed",
  0x015B => "JPEGTables",
  0x015F => "OPIProxy",
  0x0200 => "JPEGProc",
  0x0201 => "JPEGInterchangeFormat",
  0x0202 => "JPEGInterchangeFormatLength",
  0x0203 => "JPEGRestartInterval",
  0x0205 => "JPEGLosslessPredictors",
  0x0206 => "JPEGPointTransforms",
  0x0207 => "JPEGQTables",
  0x0208 => "JPEGDCTables",
  0x0209 => "JPEGACTables",
  0x0211 => "YCbCrCoefficients",
  0x0212 => "YCbCrSubSampling",
  0x0213 => "YCbCrPositioning",
  0x0214 => "ReferenceBlackWhite",
  0x02BC => "ExtensibleMetadataPlatform", /* XAP: Extensible Authoring Publishing, obsoleted by XMP: Extensible Metadata Platform */
  0x0301 => "Gamma",
  0x0302 => "ICCProfileDescriptor",
  0x0303 => "SRGBRenderingIntent",
  0x0320 => "ImageTitle",
  0x5001 => "ResolutionXUnit",
  0x5002 => "ResolutionYUnit",
  0x5003 => "ResolutionXLengthUnit",
  0x5004 => "ResolutionYLengthUnit",
  0x5005 => "PrintFlags",
  0x5006 => "PrintFlagsVersion",
  0x5007 => "PrintFlagsCrop",
  0x5008 => "PrintFlagsBleedWidth",
  0x5009 => "PrintFlagsBleedWidthScale",
  0x500A => "HalftoneLPI",
  0x500B => "HalftoneLPIUnit",
  0x500C => "HalftoneDegree",
  0x500D => "HalftoneShape",
  0x500E => "HalftoneMisc",
  0x500F => "HalftoneScreen",
  0x5010 => "JPEGQuality",
  0x5011 => "GridSize",
  0x5012 => "ThumbnailFormat",
  0x5013 => "ThumbnailWidth",
  0x5014 => "ThumbnailHeight",
  0x5015 => "ThumbnailColorDepth",
  0x5016 => "ThumbnailPlanes",
  0x5017 => "ThumbnailRawBytes",
  0x5018 => "ThumbnailSize",
  0x5019 => "ThumbnailCompressedSize",
  0x501A => "ColorTransferFunction",
  0x501B => "ThumbnailData",
  0x5020 => "ThumbnailImageWidth",
  0x5021 => "ThumbnailImageHeight",
  0x5022 => "ThumbnailBitsPerSample",
  0x5023 => "ThumbnailCompression",
  0x5024 => "ThumbnailPhotometricInterp",
  0x5025 => "ThumbnailImageDescription",
  0x5026 => "ThumbnailEquipMake",
  0x5027 => "ThumbnailEquipModel",
  0x5028 => "ThumbnailStripOffsets",
  0x5029 => "ThumbnailOrientation",
  0x502A => "ThumbnailSamplesPerPixel",
  0x502B => "ThumbnailRowsPerStrip",
  0x502C => "ThumbnailStripBytesCount",
  0x502D => "ThumbnailResolutionX",
  0x502E => "ThumbnailResolutionY",
  0x502F => "ThumbnailPlanarConfig",
  0x5030 => "ThumbnailResolutionUnit",
  0x5031 => "ThumbnailTransferFunction",
  0x5032 => "ThumbnailSoftwareUsed",
  0x5033 => "ThumbnailDateTime",
  0x5034 => "ThumbnailArtist",
  0x5035 => "ThumbnailWhitePoint",
  0x5036 => "ThumbnailPrimaryChromaticities",
  0x5037 => "ThumbnailYCbCrCoefficients",
  0x5038 => "ThumbnailYCbCrSubsampling",
  0x5039 => "ThumbnailYCbCrPositioning",
  0x503A => "ThumbnailRefBlackWhite",
  0x503B => "ThumbnailCopyRight",
  0x5090 => "LuminanceTable",
  0x5091 => "ChrominanceTable",
  0x5100 => "FrameDelay",
  0x5101 => "LoopCount",
  0x5110 => "PixelUnit",
  0x5111 => "PixelPerUnitX",
  0x5112 => "PixelPerUnitY",
  0x5113 => "PaletteHistogram",
  0x1000 => "RelatedImageFileFormat",
  0x800D => "ImageID",
  0x80E3 => "Matteing",   /* obsoleted by ExtraSamples */
  0x80E4 => "DataType",   /* obsoleted by SampleFormat */
  0x80E5 => "ImageDepth",
  0x80E6 => "TileDepth",
  0x828D => "CFARepeatPatternDim",
  0x828E => "CFAPattern",
  0x828F => "BatteryLevel",
  0x8298 => "Copyright",
  0x829A => "ExposureTime",
  0x829D => "FNumber",
  0x83BB => "IPTC/AA",
  0x84E3 => "IT8RasterPadding",
  0x84E5 => "IT8ColorTable",
  0x8649 => "ImageResourceInformation", /* PhotoShop */
  0x8769 => "Exif_IFD_Pointer",
  0x8773 => "ICC_Profile",
  0x8822 => "ExposureProgram",
  0x8824 => "SpectralSensity",
  0x8828 => "OECF",
  0x8825 => "GPS_IFD_Pointer",
  0x8827 => "ISOSpeedRatings",
  0x8828 => "OECF",
  0x9000 => "ExifVersion",
  0x9003 => "DateTimeOriginal",
  0x9004 => "DateTimeDigitized",
  0x9101 => "ComponentsConfiguration",
  0x9102 => "CompressedBitsPerPixel",
  0x9201 => "ShutterSpeedValue",
  0x9202 => "ApertureValue",
  0x9203 => "BrightnessValue",
  0x9204 => "ExposureBiasValue",
  0x9205 => "MaxApertureValue",
  0x9206 => "SubjectDistance",
  0x9207 => "MeteringMode",
  0x9208 => "LightSource",
  0x9209 => "Flash",
  0x920A => "FocalLength",
  0x920B => "FlashEnergy",                 /* 0xA20B  in JPEG   */
  0x920C => "SpatialFrequencyResponse",    /* 0xA20C    -  -    */
  0x920D => "Noise",
  0x920E => "FocalPlaneXResolution",       /* 0xA20E    -  -    */
  0x920F => "FocalPlaneYResolution",       /* 0xA20F    -  -    */
  0x9210 => "FocalPlaneResolutionUnit",    /* 0xA210    -  -    */
  0x9211 => "ImageNumber",
  0x9212 => "SecurityClassification",
  0x9213 => "ImageHistory",
  0x9214 => "SubjectLocation",             /* 0xA214    -  -    */
  0x9215 => "ExposureIndex",               /* 0xA215    -  -    */
  0x9216 => "TIFF/PStandardID",
  0x9217 => "SensingMethod",               /* 0xA217    -  -    */
  0x923F => "StoNits",
  0x927C => "MakerNote",
  0x9286 => "UserComment",
  0x9290 => "SubSecTime",
  0x9291 => "SubSecTimeOriginal",
  0x9292 => "SubSecTimeDigitized",
  0x935C => "ImageSourceData",             /* "Adobe Photoshop Document Data Block": 8BIM... */
  0x9c9b => "Title",                      /* Win XP specific, Unicode  */
  0x9c9c => "Comments",                   /* Win XP specific, Unicode  */
  0x9c9d => "Author",                     /* Win XP specific, Unicode  */
  0x9c9e => "Keywords",                   /* Win XP specific, Unicode  */
  0x9c9f => "Subject",                    /* Win XP specific, Unicode, not to be confused with SubjectDistance and SubjectLocation */
  0xA000 => "FlashPixVersion",
  0xA001 => "ColorSpace",
  0xA002 => "ExifImageWidth",
  0xA003 => "ExifImageLength",
  0xA004 => "RelatedSoundFile",
  0xA005 => "InteroperabilityOffset",
  0xA20B => "FlashEnergy",                 /* 0x920B in TIFF/EP */
  0xA20C => "SpatialFrequencyResponse",    /* 0x920C    -  -    */
  0xA20D => "Noise",
  0xA20E => "FocalPlaneXResolution",        /* 0x920E    -  -    */
  0xA20F => "FocalPlaneYResolution",       /* 0x920F    -  -    */
  0xA210 => "FocalPlaneResolutionUnit",    /* 0x9210    -  -    */
  0xA211 => "ImageNumber",
  0xA212 => "SecurityClassification",
  0xA213 => "ImageHistory",
  0xA214 => "SubjectLocation",             /* 0x9214    -  -    */
  0xA215 => "ExposureIndex",               /* 0x9215    -  -    */
  0xA216 => "TIFF/PStandardID",
  0xA217 => "SensingMethod",               /* 0x9217    -  -    */
  0xA300 => "FileSource",
  0xA301 => "SceneType",
  0xA302 => "CFAPattern",
  0xA401 => "CustomRendered",
  0xA402 => "ExposureMode",
  0xA403 => "WhiteBalance",
  0xA404 => "DigitalZoomRatio",
  0xA405 => "FocalLengthIn35mmFilm",
  0xA406 => "SceneCaptureType",
  0xA407 => "GainControl",
  0xA408 => "Contrast",
  0xA409 => "Saturation",
  0xA40A => "Sharpness",
  0xA40B => "DeviceSettingDescription",
  0xA40C => "SubjectDistanceRange",
  0xA420 => "ImageUniqueID"
);

//--------------------------------------------------------------------------
// Parse the marker stream until SOS or EOI is seen;
//--------------------------------------------------------------------------
function _exif_php_read_jpeg_sections(&$II, &$infile)
{
    $a = fgetc($infile);

    if (ord($a) != 0xff || ord(fgetc($infile)) != 0xD8)
        return false;

    while(!feof($infile))
    {
        $marker = 0;

        for ($a=0;$a<7;$a++)
        {
            $marker = fgetc($infile);
            if ($marker===false)
                return false;

            $marker=ord($marker);

            if ($marker != 0xff)
                break;

            if ($a >= 6)
                return false;
        }

        if ($marker == 0xff)
            // 0xff is legal padding, but if we get that many, something's wrong.
            return false;
        if ($marker== 0xda || $marker==0xd9)
            return true;

        // Read the length of the section.
        $lh = ord(fgetc($infile));
        $ll = ord(fgetc($infile));

        $itemlen = ($lh << 8) | $ll;

        if ($itemlen < 2)
        {
            // Invalid marker
            return false;
        }

        if (0xE1==$marker ||
            (($marker>=0xC0 && $marker<=0xCF) && $marker!=0xC4 && $marker!=0xCC))
        {
            // Store first two pre-read bytes.
            fseek($infile,-2,SEEK_CUR);
            $Data= fread($infile, $itemlen); // Read the whole section.
            if (strlen($Data)!=$itemlen)
                return false;

            if (0xE1==$marker)
            {
                // Seen files from some 'U-lead' software with Vivitar scanner
                // that uses marker 31 for non exif stuff.  Thus make sure
                // it says 'Exif' in the section before treating it as exif.
                if (substr($Data,2,4)=="Exif")
                    _exif_php_process_exif($II,$Data);
            }
            else
            {
                $d=unpack("nlen/Cprecision/nheight/nwidth/Cnumcomp",$Data);
                if (count($d)==5)
                {
                    $II['COMPUTED']['Height']=$d['height'];
                    $II['COMPUTED']['Width']=$d['width'];
                    $II['COMPUTED']['html']="width=\"{$d['width']}\" height=\"{$d['height']}\"";
                    $II['COMPUTED']['IsColor']=(($d['numcomp']==3)?1:0);
                }
            }
        }
        else
            if (fseek($infile,$itemlen-2,SEEK_CUR)==-1)
                return false;
    }
    return true;
}

function _exif_php_get16u(&$str,$off,$Motorola)
{
    $a=@unpack($Motorola?"na":"va",substr($str,$off,2));
    return $a['a'];
}
function _exif_php_get32u(&$str,$off,$Motorola)
{
    $a=@unpack($Motorola?"Na":"Va",substr($str,$off,4));
    return $a['a'];
}

//--------------------------------------------------------------------------
// Process one of the nested EXIF directories.
//--------------------------------------------------------------------------
function _exif_php_process_exif_dir(&$II, &$Data, $nDirOffsetBase, $NestingLevel, $Motorola, $path, &$names)
{
    global $_g_exifTagTable;
    if ($NestingLevel > 4)
        return;

    if (empty($names))
        $names=&$_g_exifTagTable;

    static $BytesPerFormat = array(0,1,1,2,4,8,1,1,2,4,8,4,8);

    $NumDirEntries = _exif_php_get16u($Data,$nDirOffsetBase,$Motorola);
    if ($NumDirEntries>200)
        return;

    for ($de=0;$de<$NumDirEntries;$de++)
    {
        $DirEntry = $nDirOffsetBase +2+12*$de;

        if (strlen($Data)<$DirEntry+8)
            return;

        $Tag = _exif_php_get16u($Data,          $DirEntry     , $Motorola);
        $Format = _exif_php_get16u($Data,   $DirEntry + 2 , $Motorola);
        $Components = _exif_php_get32u($Data, $DirEntry + 4 , $Motorola);

        if ($Format-1 >= NUM_FORMATS)
        {
            // (-1) catches illegal zero case as unsigned underflows to positive large.
            continue;
        }

        $ByteCount = $Components * $BytesPerFormat[$Format];

        if ($ByteCount > 4)
        {
            $OffsetVal = _exif_php_get32u($Data, $DirEntry+8 , $Motorola);
            $ValuePtr = $OffsetVal;
        }
        else
        {
            // 4 bytes or less and value is in the dir entry itself
            $ValuePtr = $DirEntry+8;
        }

        if (!isset($names[$Tag]))
            $sTag=sprintf("UndefinedTag:0x%04x" ,$Tag);
        else
            $sTag=$names[$Tag];

        // Convert type
        unset($v);
        switch ($Format)
        {
            case FMT_SBYTE:
            case FMT_BYTE:
                $v=array();
                for ($i=0;$i<$Components;++$i)
                    $v[]=ord(substr($Data,$ValuePtr+$i,1));
                break;

            case FMT_USHORT:
            case FMT_SSHORT:
                $v=array();
                for ($i=0;$i<$Components;++$i)

                    $v[]=_exif_php_get16u($Data,$ValuePtr+$i*2,$Motorola);

                break;

            case FMT_URATIONAL:
            case FMT_SRATIONAL:
                $v=array();
                for ($i=0;$i<$Components;++$i)
                {
                    $Num = _exif_php_get32u($Data,$ValuePtr+$i*8,$Motorola);
                    $Den = _exif_php_get32u($Data,$ValuePtr+$i*8+4,$Motorola);

                    if ($Den == 0)
                        $v[]="";
                    else
                        $v[]="$Num/$Den";
                }

                break;

            case FMT_SLONG:
            case FMT_ULONG:
                for ($i=0;$i<$Components;++$i)
                    $v[]=(int)_exif_php_get32u($Data,$ValuePtr,$Motorola);
                break;

            case FMT_STRING:
                // remove extra 00
                $v=substr($Data, $ValuePtr, $ByteCount);
                while (!ord(substr($v,-1)) && strlen($v))
                    $v=substr($v,0,-1);
                break;
            case FMT_UNDEFINED:
                $v=substr($Data, $ValuePtr, $ByteCount);
                break;
        }


        if (isset($v))
        {
            if (is_array($v) && count($v)==1)
                $v=$v[0];

            if (empty($path))
            {
                if (!isset($II[$sTag]))
                    $II[$sTag]=$v;
            }
            else
            {
                if (!isset($II[$path][$sTag]))
                    $II[$path][$sTag]=$v;
            }
        }
        if ($Tag==0x927C)
        {
            if (isset($II['IFD0']['Make']))
                $make=trim($II['IFD0']['Make']);
            else
                $make="";


            $_names=array();
            $_bMotorola=$Motorola;
            switch ($make)
            {
                case 'Canon':
                    $_names=array(
                        0x0001 => "ModeArray",
                        0x0004 => "ImageInfo",
                        0x0006 => "ImageType",
                        0x0007 => "FirmwareVersion",
                        0x0008 => "ImageNumber",
                        0x0009 => "OwnerName",
                        0x000C => "Camera",
                        0x000F => "CustomFunctions",
                    );
                    $bMotorola=false;
                    $knownMake=true;
                    break;
            }

            if (!empty($_names))
            {
                _exif_php_process_exif_dir($II, $Data, $ValuePtr, $NestingLevel+1, $_bMotorola, "MAKERNOTE", $_names);
            }
        }
        if ($Tag==TAG_EXIF_OFFSET || $Tag==TAG_INTEROP_OFFSET)
        {
            $SubdirStart = _exif_php_get32u($Data,$ValuePtr,$Motorola);
            if ($SubdirStart >= 0)
            {
                $p=($Tag==TAG_INTEROP_OFFSET)?"INTEROP":"EXIF";
                _exif_php_process_exif_dir($II, $Data, $SubdirStart, $NestingLevel+1, $Motorola, $p);
            }
        }
    }

    // In addition to linking to subdirectories via exif tags,
    // there's also a potential link to another directory at the end of each
    // directory.  this has got to be the result of a comitee!
    if ($nDirOffsetBase+2+12*$NumDirEntries + 4 <= strlen($Data))
    {
        $Offset = _exif_php_get32u($Data, $nDirOffsetBase+2+12*$NumDirEntries,$Motorola);
        if ($Offset)
        {
            $SubdirStart = $Offset;
            _exif_php_process_exif_dir($II, $Data, $SubdirStart, $NestingLevel+1,$Motorola,"THUMBNAIL");
        }
    }
}

//--------------------------------------------------------------------------
// Process a EXIF marker
// Describes all the drivel that most digital cameras include...
//--------------------------------------------------------------------------
function _exif_php_process_exif(&$II, &$Data)
{
    if (substr($Data,2,4)!="Exif")
        return false;

    $order=substr($Data,8,2);
    switch ($order)
    {
        case "II":
            $Motorola = 0;
            break;
        case "MM":
            $Motorola = 1;
            break;
        default:
            return false;
    }
    $II['COMPUTED']['ByteOrderMotorola']=$Motorola;

    // Check the next value for correctness.
    if (_exif_php_get16u($Data,10,$Motorola)!=0x2a)
        return false;

    $FirstOffset = _exif_php_get32u($Data,12,$Motorola);

    if ($FirstOffset < 8 || $FirstOffset > 16)
    {
        // I used to ensure this was set to 8 (website I used indicated its 8)
        // but PENTAX Optio 230 has it set differently, and uses it as offset. (Sept 11 2002)
        return false;
    }

    // First directory starts 16 bytes in.  All offset are relative to 8 bytes in.
    _exif_php_process_exif_dir($II,substr($Data,8), $FirstOffset, 0, $Motorola, "IFD0");
    return true;
}

//--------------------------------------------------------------------------
// Read image data.
//--------------------------------------------------------------------------
function &exif_php_read_data($FileName,$bArray=false)
{
    $infile = fopen($FileName, "rb"); // Unix ignores 'b', windows needs it.

    if ($infile == null)
        return false;

    $II=array();

    // Scan the JPEG headers.
    $ret = _exif_php_read_jpeg_sections($II,$infile);
    fclose($infile);
    if (!$ret)
        return false;

    // Add [FILE] section
    $II['FILE']['FileName'] = basename(strval($FileName));
    $II['FILE']['FileDateTime'] = @filemtime($FileName);
    $II['FILE']['FileSize'] = @filesize($FileName);
    $II['FILE']['FileType'] = 2;            // We don't process anything but JPEGs
    $II['FILE']['MimeType'] = "image/jpeg";

    // Calculate ApertureFNumber
    if (!isset($II['COMPUTED']['ApertureFNumber']))
    {
        unset($v);

        $simple=false;
        if (isset($II['EXIF']['FNumber']))
        {
            $simple=true;
            $v=$II['EXIF']['FNumber'];
        }
        else if (isset($II['EXIF']['ApertureValue']))
            $v=$II['EXIF']['ApertureValue'];
        else if (isset($II['EXIF']['MaxApertureValue']))
            $v=$II['EXIF']['MaxApertureValue'];

        if (!empty($v))
        {
            $x=split('/',$v);
            if (count($x)>=2 && $x[1]>0)
            {
                $b=(double)$x[0]/$x[1];
                if (!$simple)
                    $b=exp($b*log(2)*0.5);
                $II['COMPUTED']['ApertureFNumber']=sprintf("f/%.1f",$b);
            }
            else
                $II['COMPUTED']['ApertureFNumber']=$v;
        }

    }

    if ($bArray)
        return $II;

    //print_r($II);
    reset($II);
    $IR=array();
    while (list($key, $val) = each($II))
    {
        if ($key=='COMPUTED' || $key=='THUMBNAIL')
            $IR[$key]=$val;
        else
            $IR=array_merge($IR,$val);
    }
    return $IR;
}
?>
