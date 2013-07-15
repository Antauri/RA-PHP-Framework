<?php
/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Provides support for graphics, via image cropping, resizing or video processing;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Graphics.php 1 2012-10-26 08:27:37Z root $
 */
final class Graphics {
    /**
     * Resizes images on the given directory, through the given files and at the given dimensions, keeping ratio;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Graphics.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function resizeImages (StorageDirectoryPath $objDirectory, A $objFiles, A $objDimensions) {
        // Foreach
        foreach ($objFiles as $objKF => $objVF) {
            // Check
            if (($imagePath = new StoragePath ($objDirectory .
            $objVF['name'], FALSE)) && $imagePath
            ->checkPathExists ()) {

                // Process
                list ($objImgWidth, $objImgHeight) =
                $objImgInfo = getimagesize ($imagePath
                ->toAbsolutePath ());

                // Get the type
                if ($objVF['type'] !=
                image_type_to_mime_type
                ($objImgInfo[2])) {
                    // Set
                    $objVF['type'] = new
                    S (image_type_to_mime_type
                    ($objImgInfo[2]));
                }

                // Resourcefy
                if (($objCurrentImage = self::getImageFromType (new
                A ($objVF), $imagePath)) != NULL) {
                    // Set
                    $objCurrentProcessing =
                    new R ($objCurrentImage);
                } else {
                    // Go
                    continue;
                }

                // Go further,
                if ($objCurrentProcessing
                ->checkIs ('res')->toBoolean ()) {
                    // Foreach
                    foreach ($objDimensions as $objK => $objV) {
                        // Ratio
                        $objRatio = $objImgWidth/$objImgHeight;

                        // Check
                        if ($objK / $objV > $objRatio) {
                            // Set
                            $objThumbWidth  = ceil ($objV * $objRatio);
                            $objThumbHeight = $objV;
                        } else {
                            // Set
                            $objThumbHeight = ceil ($objK / $objRatio);
                            $objThumbWidth  = $objK;
                        }

                        // Create the temporary;
                        $temporaryImage = new R (imagecreatetruecolor
                        ($objThumbWidth, $objThumbHeight));

                        // Check if it's PNG or GIF
                        if ($objVF['type'] == 'image/png'
                        or  $objVF['type'] == 'image/gif') {
                            // Make it
                            imagealphablending ($tempImg = $temporaryImage->toResource (), FALSE);
                            imagesavealpha ($tempImg, TRUE);
                            $imageTrans = imagecolorallocatealpha ($tempImg, 255, 255, 255, 127);
                            imagefilledrectangle ($tempImg, 0, 0, $objImgWidth, $objImgHeight, $imageTrans);
                            $temporaryImage = new R ($tempImg);
                        }

                        // Copy
                        if (imagecopyresampled ($temporaryImage
                        ->toResource (), $objCurrentProcessing->toResource (), 0, 0, 0, 0,
                        $objThumbWidth, $objThumbHeight, $objImgWidth, $objImgHeight)) {
                            // Set
                            $objToDir = $objDirectory->toAbsolutePath ()
                            ->appendString ((string) $objK)->appendString (_U)
                            ->appendString ((string) $objV)->appendString (_U)
                            ->appendString ((string) $objVF['name']);

                            // Switch
                            switch ($objVF['type']) {
                                case 'image/gif'  :
                                    imagegif ($temporaryImage
                                    ->toResource (), $objToDir);
                                    break;

                                case 'image/png'  :
                                    imagepng ($temporaryImage
                                    ->toResource (), $objToDir);
                                    break;

                                case 'image/bmp'  :
                                    imagewbmp ($temporaryImage
                                    ->toResource (), $objToDir);
                                    break;

                                case 'image/jpeg' :
                                    imagejpeg ($temporaryImage
                                    ->toResource (), $objToDir);
                                    break;
                            }
                        }

                        // Erase
                        imagedestroy ($temporaryImage->toResource ());
                        unset ($temporaryImage);
                    }

                    // Erase
                    imagedestroy ($objCurrentProcessing->toResource ());
                    unset ($objCurrentProcessing);
                }
            }
        }
    }

    /**
     * Returns an image resources, checking agains type;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Graphics.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getImageFromType (A $uploadedImageType, StoragePath $imagePath) {
        // Switch
        switch ($uploadedImageType
        ->offsetGet ('type')) {
            // Cases
            case 'image/gif'  : return imagecreatefromgif  ($imagePath->toAbsolutePath ()); break;
            case 'image/png'  : return imagecreatefrompng  ($imagePath->toAbsolutePath ()); break;
            case 'image/wbmp' : return imagecreatefromwbmp ($imagePath->toAbsolutePath ()); break;
            case 'image/jpeg' : return imagecreatefromjpeg ($imagePath->toAbsolutePath ()); break;

            default:
                // Throws
                throw new UnknownImageTypeException;
                break;
        }
    }
}
?>
