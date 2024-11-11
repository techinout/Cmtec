/*!
  * Bootstrap tab.js v5.2.3 (https://getbootstrap.com/)
  * Copyright 2011-2022 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
  * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
  */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory(require('./util/index'), require('./dom/event-handler'), require('./dom/selector-engine'), require('./base-component')) :
  typeof define === 'function' && define.amd ? define(['./util/index', './dom/event-handler', './dom/selector-engine', './base-component'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.Tab = factory(global.Index, global.EventHandler, global.SelectorEngine, global.BaseComponent));
})(this, (function (index, EventHandler, SelectorEngine, BaseComponent) { 'use strict';

  const _interopDefaultLegacy = e => e && typeof e === 'object' && 'default' in e ? e : { default: e };

  const EventHandler__default = /*#__PURE__*/_interopDefaultLegacy(EventHandler);
  const SelectorEngine__default = /*#__PURE__*/_interopDefaultLegacy(SelectorEngine);
  const BaseComponent__default = /*#__PURE__*/_interopDefaultLegacy(BaseComponent);

  /**
   * --------------------------------------------------------------------------
   * Bootstrap (v5.2.3): tab.js
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
   * --------------------------------------------------------------------------
   */
  /**
   * Constants
   */

  const NAME = 'tab';
  const DATA_KEY = 'bs.tab';
  const EVENT_KEY = `.${DATA_KEY}`;
  const EVENT_HIDE = `hide${EVENT_KEY}`;
  const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
  const EVENT_SHOW = `show${EVENT_KEY}`;
  const EVENT_SHOWN = `shown${EVENT_KEY}`;
  const EVENT_CLICK_DATA_API = `click${EVENT_KEY}`;
  const EVENT_KEYDOWN = `keydown${EVENT_KEY}`;
  const EVENT_LOAD_DATA_API = `load${EVENT_KEY}`;
  const ARROW_LEFT_KEY = 'ArrowLeft';
  const ARROW_RIGHT_KEY = 'ArrowRight';
  const ARROW_UP_KEY = 'ArrowUp';
  const ARROW_DOWN_KEY = 'ArrowDown';
  const CLASS_NAME_ACTIVE = 'active';
  const CLASS_NAME_FADE = 'fade';
  const CLASS_NAME_SHOW = 'show';
  const CLASS_DROPDOWN = 'dropdown';
  const SELECTOR_DROPDOWN_TOGGLE = '.dropdown-toggle';
  const SELECTOR_DROPDOWN_MENU = '.dropdown-menu';
  const NOT_SELECTOR_DROPDOWN_TOGGLE = ':not(.dropdown-toggle)';
  const SELECTOR_TAB_PANEL = '.list-group, .nav, [role="tablist"]';
  const SELECTOR_OUTER = '.nav-item, .list-group-item';
  const SELECTOR_INNER = `.nav-link${NOT_SELECTOR_DROPDOWN_TOGGLE}, .list-group-item${NOT_SELECTOR_DROPDOWN_TOGGLE}, [role="tab"]${NOT_SELECTOR_DROPDOWN_TOGGLE}`;
  const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="tab"], [data-bs-toggle="pill"], [data-bs-toggle="list"]'; // todo:v6: could be only `tab`

  const SELECTOR_INNER_ELEM = `${SELECTOR_INNER}, ${SELECTOR_DATA_TOGGLE}`;
  const SELECTOR_DATA_TOGGLE_ACTIVE = `.${CLASS_NAME_ACTIVE}[data-bs-toggle="tab"], .${CLASS_NAME_ACTIVE}[data-bs-toggle="pill"], .${CLASS_NAME_ACTIVE}[data-bs-toggle="list"]`;
  /**
   * Class definition
   */

  class Tab extends BaseComponent__default.default {
    constructor(element) {
      super(element);
      this._parent = this._element.closest(SELECTOR_TAB_PANEL);

      if (!this._parent) {
        return; // todo: should Throw exception on v6
        // throw new TypeError(`${element.outerHTML} has not a valid parent ${SELECTOR_INNER_ELEM}`)
      } // Set up initial aria attributes


      this._setInitialAttributes(this._parent, this._getChildren());

      EventHandler__default.default.on(this._element, EVENT_KEYDOWN, event => this._keydown(event));
    } // Getters


    static get NAME() {
      return NAME;
    } // Public


    show() {
      // Shows this elem and deactivate the active sibling if exists
      const innerElem = this._element;

      if (this._elemIsActive(innerElem)) {
        return;
      } // Search for active tab on same parent to deactivate it


      const active = this._getActiveElem();

      const hideEvent = active ? EventHandler__default.default.trigger(active, EVENT_HIDE, {
        relatedTarget: innerElem
      }) : null;
      const showEvent = EventHandler__default.default.trigger(innerElem, EVENT_SHOW, {
        relatedTarget: active
      });

      if (showEvent.defaultPrevented || hideEvent && hideEvent.defaultPrevented) {
        return;
      }

      this._deactivate(active, innerElem);

      this._activate(innerElem, active);
    } // Private


    _activate(element, relatedElem) {
      if (!element) {
        return;
      }

      element.classList.add(CLASS_NAME_ACTIVE);

      this._activate(index.getElementFromSelector(element)); // Search and activate/show the proper section


      const complete = () => {
        if (element.getAttribute('role') !== 'tab') {
          element.classList.add(CLASS_NAME_SHOW);
          return;
        }

        element.removeAttribute('tabindex');
        element.setAttribute('aria-selected', true);

        this._toggleDropDown(element, true);

        EventHandler__default.default.trigger(element, EVENT_SHOWN, {
          relatedTarget: relatedElem
        });
      };

      this._queueCallback(complete, element, element.classList.contains(CLASS_NAME_FADE));
    }

    _deactivate(element, relatedElem) {
      if (!element) {
        return;
      }

      element.classList.remove(CLASS_NAME_ACTIVE);
      element.blur();

      this._deactivate(index.getElementFromSelector(element)); // Search and deactivate the shown section too


      const complete = () => {
        if (element.getAttribute('role') !== 'tab') {
          element.classList.remove(CLASS_NAME_SHOW);
          return;
        }

        element.setAttribute('aria-selected', false);
        element.setAttribute('tabindex', '-1');

        this._toggleDropDown(element, false);

        EventHandler__default.default.trigger(element, EVENT_HIDDEN, {
          relatedTarget: relatedElem
        });
      };

      this._queueCallback(complete, element, element.classList.contains(CLASS_NAME_FADE));
    }

    _keydown(event) {
      if (![ARROW_LEFT_KEY, ARROW_RIGHT_KEY, ARROW_UP_KEY, ARROW_DOWN_KEY].includes(event.key)) {
        return;
      }

      event.stopPropagation(); // stopPropagation/preventDefault both added to support up/down keys without scrolling the page

      event.preventDefault();
      const isNext = [ARROW_RIGHT_KEY, ARROW_DOWN_KEY].includes(event.key);
      const nextActiveElement = index.getNextActiveElement(this._getChildren().filter(element => !index.isDisabled(element)), event.target, isNext, true);

      if (nextActiveElement) {
        nextActiveElement.focus({
          preventScroll: true
        });
        Tab.getOrCreateInstance(nextActiveElement).show();
      }
    }

    _getChildren() {
      // collection of inner elements
      return SelectorEngine__default.default.find(SELECTOR_INNER_ELEM, this._parent);
    }

    _getActiveElem() {
      return this._getChildren().find(child => this._elemIsActive(child)) || null;
    }

    _setInitialAttributes(parent, children) {
      this._setAttributeIfNotExists(parent, 'role', 'tablist');

      for (const child of children) {
        this._setInitialAttributesOnChild(child);
      }
    }

    _setInitialAttributesOnChild(child) {
      child = this._getInnerElement(child);

      const isActive = this._elemIsActive(child);

      const outerElem = this._getOuterElement(child);

      child.setAttribute('aria-selected', isActive);

      if (outerElem !== child) {
        this._setAttributeIfNotExists(outerElem, 'role', 'presentation');
      }

      if (!isActive) {
        child.setAttribute('tabindex', '-1');
      }

      this._setAttributeIfNotExists(child, 'role', 'tab'); // set attributes to the related panel too


      this._setInitialAttributesOnTargetPanel(child);
    }

    _setInitialAttributesOnTargetPanel(child) {
      const target = index.getElementFromSelector(child);

      if (!target) {
        return;
      }

      this._setAttributeIfNotExists(target, 'role', 'tabpanel!‚â¿\êùê|ÿúîÿûğ…ÿüôŠÿıöÿıø—ÿıùÿşú¤ÿşûªÿÚúÂÿú÷ÿ£ÿÿÿ ÿÿÿšÿÿÿùÿÿ[íÿÿBâÿÿ5Øÿÿ/Ñÿÿ+Êÿÿ(Àÿÿ(»ÿÿ+ºÿÿ3ÀÿÿIÍúÿ¦İÆÿõñŸÿşú£ÿÿû¢ÿÿû¡ÿÿûŸÿÿúÿÿù›ÿşù˜ÿşø”ÿş÷‘ÿşõÿşóˆÿıò„ÿıï€ÿıìzÿüéuÿûåpÿùálÿ÷ÛgÿõÕcÿòÏ^ÿğÉZÿíÀVÿëºSÿå³PşÂŒ3á¥ka                         ÿ'­ÿ†#¤û Y                                                                                                                             ¸y ~à½[èøè}ÿúíÿûğ„ÿüóŠÿıõÿı÷–ÿıù›ÿıú¢ÿşú§ÿîú¶ÿ”úîÿŸşÿÿ¢ÿÿÿÿÿÿŠûÿÿeğÿÿHåÿÿ8Ûÿÿ0Óÿÿ,Ìÿÿ)Âÿÿ(¼ÿÿ*ºÿÿ/½ÿÿ:ÈÿÿzÖàÿåê¡ÿü÷ÿşúÿşúœÿşú™ÿşù—ÿşù•ÿş÷’ÿşöÿşô‰ÿşó…ÿşğÿıí{ÿüévÿûårÿúàmÿøÛiÿöÕdÿóÏ_ÿğË[ÿíÀWÿë»Tÿä³Pı»ƒ,Ö£kQ                                ,ªüs§ÿ£H™#                                                                                                                                ™f3µx!lÕ¨Iİöå|şùë€ÿûï„ÿüò‰ÿüôÿı÷”ÿıøšÿıùŸÿşú¥ÿöú¯ÿªùİÿ”ışÿ£ÿÿÿŸÿÿÿ“ıÿÿpôÿÿOçÿÿ<Şÿÿ3Öÿÿ-Îÿÿ*Çÿÿ(¾ÿÿ)»ÿÿ-»ÿÿ6ÃÿÿTĞôÿ¼áµÿ÷ò–ÿşù–ÿşù•ÿşø“ÿş÷ÿşöÿıõ‹ÿıó…ÿıñÿıî}ÿüêxÿúåtÿùánÿ÷ÛjÿõÕeÿòĞ`ÿğÊ\ÿíÁYÿê¼Vÿà®Mùµ}&Ì¢j7                                    )ªúi §ÿ¬"k¿4                                                                                                                                     ´tDÆ“8Èğ×s÷ùëÿúî‚ÿûñˆÿüô‹ÿüö‘ÿı÷—ÿşùœÿşú¢ÿüú¨ÿÉùÈÿŒúøÿ¡ÿÿÿ¡ÿÿÿ™şÿÿ~øÿÿ[ìÿÿDáÿÿ6Ùÿÿ/Ñÿÿ+Ëÿÿ)Áÿÿ(¼ÿÿ+»ÿÿ2¿ÿÿ=ÉÿÿØØÿäé–ÿûôÿıöŒÿıö‹ÿıôˆÿıó…ÿıñÿüî}ÿûêyÿúåtÿøáoÿ÷ÛkÿõÔfÿòĞbÿïÊ^ÿìÀZÿê»XÿÕ¡Dî®t!§¤j                                        &ªüi!¨ÿ¹yÏA                                                                                                                                            °u·}#á¾^èöåşúì‚ÿûï†ÿûò‰ÿüõÿü÷’ÿıø™ÿıùŸÿşú¤ÿêú´ÿ—ùèÿ˜ışÿ£ÿÿÿÿÿÿûÿÿjñÿÿLæÿÿ<Üÿÿ2Ôÿÿ-Îÿÿ*Æÿÿ(¾ÿÿ)¼ÿÿ.½ÿÿ7ÄÿÿMÏøÿ±Ü²ÿòì†ÿûòƒÿüñ‚ÿüïÿûì{ÿúèxÿùätÿøßpÿ÷ÚlÿõÕhÿòÏdÿïË_ÿìÃ\ÿä¶VûÂŒ5Û¨nv¿                                             )ªú|¨ÿÃ!ÖE                                                                                                                                                ¶m$´uYÉ•;ÎîÓqõøè€ÿúíƒÿûñ‡ÿûó‹ÿüõÿı÷•ÿıø™ÿıùŸÿøú§ÿÁùÌÿ‹ù÷ÿ¡şÿÿ şÿÿ—ıÿÿyõÿÿYêÿÿCàÿÿ5Øÿÿ/Ğÿÿ+Êÿÿ)Áÿÿ)½ÿÿ+¼ÿÿ2Àÿÿ<ÈÿÿlÓáÿÔß“ÿ÷çzÿùçyÿùävÿùásÿ÷İqÿöÙmÿõÔiÿóĞeÿğÊbÿìÂ^şÕ£Gë²y$°ªo0ÿ                                                  )«û§ §ÿÎ$}Ò?                                                                                                                                                        ³z¶{"~Ñ£FÙñØu÷ùéÿúî„ÿûñ‡ÿüóŒÿüõÿı÷•ÿıøšÿıùÿçú°ÿ øáÿûıÿ¢şÿÿşÿÿŒúÿÿjğÿÿNåÿÿ<Üÿÿ2Ôÿÿ-Íÿÿ*Åşÿ)¿şÿ*»şÿ-¼şÿ4ÀşÿBËûÿ‚ÓÎÿÜØ„ÿõ